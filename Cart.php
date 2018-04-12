<?php


/**
 * Class Cart
 */
class Cart
{

    protected $productList;
    protected $discountRules;
    protected $cart;
    protected $complementaryItemsList;


    /**
     * Cart constructor.
     * @param $productList
     * @param $discountRules
     */
    public function __construct($productList, $discountRules)
    {
        $this->cart = array();
        $this->productList = $productList;
        $this->discountRules = $discountRules;
        $this->complementaryItemsList = array(); /*will use this to hold git items when price is being calculated*/
    }

    /*
 * add only products that we have information about....e.g price.
 * unknown products will not be added to cart
 *  Less then 1 cannot be added to cart..quantity must be greater than 1
 * if already in cart, just update quantity
*/
    /**
     * @param $productID
     * @param int $count
     */
    public function addToCart($productID, $count = 1)
    {
        if($count<=0)
        {
            return ;
        }

        /*cannot add products not in inventory*/
        if(!isset($this->productList[$productID]))
            return;

        $quantity = 0;
        if(isset($this->cart[$productID])) /*update count*/
        {
            $quantity = $this->cart[$productID]['quantity'];
        } /*new item add this the first time*/

        $this->cart[$productID]= array('productID'=>$productID,'price'=>$this->productList[$productID], 'quantity'=>$quantity+$count, 'TotalPrice'=>0);
    }


    public function printCart()
    {
        echo "<br>===============Order Details Start===================<br><br>";
        foreach ($this->cart as $id => $info)
        {
            echo 'Product: '.$id.'<br>';;
            echo 'Price: '.$info['price']. '<br>';
            echo 'quantity: '.$info['quantity'],'<br>';
            echo "<br>";
        }
        echo "<br>===============End of Details====================<br>";
    }

    /*perform basic calcuation ...Quantity * price */
    /**
     * @param $productDetails
     * @return float|int
     */
    private function basicPriceCalculation($productDetails)
    {
        return $productDetails['price'] * $productDetails['quantity'];
    }

    /**
     *
     */
    public function getTotal()
    {
        /*remove/reduce quantity of  free products in the cart */
        $this->removeGiftProductsFromCart();

        $total =0;
        foreach ($this->cart as $id => $productDetails)
        {
            $total+= $this->getProductPriceAfterDiscount($id, $productDetails);
        }

        /* add the gift items to the cart after the price is calculated*/
        if($this->complementaryItemsList)
        {
            foreach ($this->complementaryItemsList as $id =>$freeQauntity)
            {
                /*update quantity to include free count*/
                if(isset($this->cart[$id]))
                {
                    /*get product details*/
                    $productDetails = $this->cart[$id];

                    $quantityInCart     =  $productDetails['quantity']+$freeQauntity;
                    $this->cart[$id] = array('productID'=>$id, 'price'=> $productDetails['price'], 'quantity'=>$quantityInCart);
                }
                else
                {
                    $gift= array('productID'=>$id,'price'=>$this->productList[$id], 'quantity'=>$freeQauntity);
                    $this->cart[$id] = $gift;
                }
            }

            echo "<br><h4>Gift received courtesy of The Nile. See cart details Below</h4><br>";
            $this->printCart();
        }
        unset($this->complementaryItemsList);
        echo '<br>';
        echo '<b>Total = </b>$'.$total;
        echo '<br>';
    }


    /*get the discount pricing rules for this product $id return -1 if none exist */
    /**
     * @param $id
     * @return int|array
     */
    private function getRules($id)
    {
        foreach ($this->discountRules as $index=>$rules)
        {
            if(isset($rules[$id]))
                return $rules[$id];
        }
        return -1;
    }

    /*Apply discounts (if any avaialble) and  return  the price)*/
    /**
     * @param $id
     * @param $productDetails
     * @return float|int
     */
    private function getProductPriceAfterDiscount($id, $productDetails)
    {
        $rules = $this->getRules($id);
        if($rules==-1)
            return $this->basicPriceCalculation($productDetails);
        $discountMethod     =  $rules['discountMethod'];

        switch ($discountMethod)
        {
            case 'new_price':
                return $this->NewPriceMethodCalculation($productDetails,$rules);
                break;

            case 'free_items':
                return $this->FreeItemsPriceMethodCalculation($id, $productDetails,$rules);
                break;

            case 'complementary':
                return $this->complementaryPriceMethodCalculation($id,$rules);
                break;

            default:
                return$this->basicPriceCalculation($productDetails);
                break;
        }
        return 0; /*should never get to this point anyway but to stop from blowing up*/
    }

    /**
     * @param $productDetails
     * @param $rules
     * @return float|int
     */
    private function NewPriceMethodCalculation($productDetails, $rules)
    {
        $quantityInCart = $productDetails['quantity'];
        $newPrice = $rules['details']['price'];

        if($quantityInCart<$rules['minOrder'])
        {
            return $this->basicPriceCalculation($productDetails);
        }
        return $newPrice*$quantityInCart;
    }

    /*if customer buys n items of a product 'A', x quantiy of that product would be free. buy 2 and get 3rd free*/
    /**
     * @param $id
     * @param $productDetails
     * @param $rules
     * @return float|int
     */
    private function freeItemsPriceMethodCalculation($id, $productDetails, $rules)
    {
        $quantityInCart     =  $productDetails['quantity'];
        $originalItemPrice  =  $productDetails['price'] ;
        $detailsArray = $rules['details'];
        $freeItemCount = 0;

        foreach ($detailsArray as $itemID=>$freeQuanity)
        {
            if($itemID==$id)
                $freeItemCount+=$freeQuanity;
        }
        return $originalItemPrice* ($quantityInCart-$freeItemCount);
    }


    /*
     * Perform basic calculation after gift items are removed/reduce from the total quantity for that product     *
     * Idea is => e.g you buy fifa 2018=> you get a 1 free controller.
     */

    /**
     * @param $id
     * @return float|int
     */
    private function complementaryPriceMethodCalculation($id)
    {
        $productDetails = $this->cart[$id];
        return $this->basicPriceCalculation($productDetails);
    }

    /**
     * @param $details
     * @return int|string
     */
    private function getProductID($details)
    {
        foreach ($details as $id => $quantity)
            return $id;
    }

    /*
     * remove from cart product(s) that are awarded (for free) to customers when they purchase product x
     *
     * Items are removed from the cart temperary until price is calculated.
     *
    */

    private function removeGiftProductsFromCart()
    {
        foreach ($this->cart as $id  => $productDetails)
        {
            $rules = $this->getRules($id);
            if($rules==-1)
            {
                continue;
            }

            $discountMethod     =  $rules['discountMethod'];
            if($discountMethod != 'complementary')
                continue;

            $quantityInCart     =  $productDetails['quantity'];
            $productIDToRemove  =  $this->getProductID($rules['details']);
            $giftCount          =  $rules['details'][$productIDToRemove];

            /* customer qualifies for a free/complementary product*/
            if($quantityInCart>=$rules['minOrder'])
            {
                /*they already have these in cart so reduce chargeable quantity*/
                if(isset($this->cart[$productIDToRemove]))
                {
                    /* keep track of these free products so we can add them back to the cart after pricing calculation is done*/

                    $productDetails     =  $this->cart[$productIDToRemove];
                    $quantityInCart     =  $productDetails['quantity'];
                    $originalItemPrice  =  $productDetails['price'] ;

                    /*customer specifically add intended gift products to cart which exceeds the free amount. charge the cust only the extra quantity */
                    if($quantityInCart>$giftCount)
                    {
                        $newQuantityInCart = $quantityInCart-$giftCount;
                        $this->cart[$productIDToRemove] = array('productID'=>$productIDToRemove, 'price'=>$originalItemPrice, 'quantity'=>$newQuantityInCart);
                    }
                    else
                    {
                        unset($this->cart[$productIDToRemove]);
                    }
                }
                $this->complementaryItemsList[$productIDToRemove] = $giftCount;


            }
        }/*end of for loop*/

    }/*end of removeGiftProductsFromCart()*/


}