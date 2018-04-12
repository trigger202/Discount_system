<?php

require_once ("Cart.php");

 $productList = array(

      '9325336130810'=>39.49 ,
      '9325336028278'=>19.99 ,
      '9780201835953'=>31.87 ,
      '9781430219484'=>28.72 ,
      '9780132071482'=>119.92,
 );

/*product in discount=>discount scheme*/

/*
 * For example...if you buy 9780201835953 X 10 => customer will get a new price of 21.99 for each product
 * if you buy 9781430219484 X 3 => customer will get the third product free. aka pay 2 items full price and third is free
 *
 * if customer buys 9325336130810 => they will automatically reicieve 9325336028278 as gift (fresh prince of bell air)
 *
 * */
$discountRules =  array(
    /*keep adding here new discount types. Make sure you follow the same structure though*/
    0=>array('9780201835953'=>array('minOrder'=>10,  'discountMethod'=>'new_price', 'details'=>array('price'=>21.99))),
    1=> array('9781430219484'=>array('minOrder'=>3,  'discountMethod'=>'free_items','details'=>array('9781430219484'=>1 ))), /*buy 3 and get the third free*/
    2=>array('9325336130810'=>array('minOrder'=>1,   'discountMethod'=>'complementary', 'details'=>array('9325336028278'=>1 ))),
    3=>array('9780132071482'=>array('minOrder'=>5,   'discountMethod'=>'complementary', 'details'=>array('9781430219484'=>1 ))),

);



//$cart = new Cart($productList, $discountRules);
//$cart->addToCart('9780201835953',9);
//$cart->addToCart('9325336028278');
//$cart->printCart();
//$cart->getTotal();
//
//
//$cart = new Cart($productList, $discountRules);
//$cart->addToCart('9781430219484',3);
//$cart->addToCart('9780132071482');
//$cart->printCart();
//$cart->getTotal();
//
//
//
//$cart = new Cart($productList, $discountRules);
//$cart->addToCart('9325336028278');
//$cart->addToCart('9780201835953');
//$cart->addToCart('9325336130810');
//$cart->printCart();
//$cart->getTotal();



$cart = new Cart($productList, $discountRules);
$cart->addToCart('9780132071482',5);
$cart->printCart();
$cart->getTotal();
$cart->printCart();

