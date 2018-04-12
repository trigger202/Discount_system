# Discount_system


Your task is to develop a system to allow for discounts to be applied
to a customers cart. The system should be flexible, allowing
for the creation of new discount types easily.

Given these products:

SKU           | Name                         | Price
--------------|------------------------------|----------
9325336130810 | Game of Thrones: Season 1    | $39.49
9325336028278 | The Fresh Prince of Bel-Air  | $19.99
9780201835953 | The Mythical Man-Month       | $31.87
9781430219484 | Coders at Work               | $28.72
9780132071482 | Artificial Intelligence      | $119.92
--------------|------------------------------|----------

Initially we would like to offer our customers these discounts:

* Buy 10 or more copies of The Mythical Man-Month, and receive them at the discounted price of $21.99
* We would like to offer a 3 for the price of 2 deal on Coders at Work. (Buy 3 get 1 free);
* Customers who purchase Game of Thrones: Season 1, will get The Fresh Prince of Bel-Air free.


Examples:

Products in cart: 9780201835953 x 10, 9325336028278
Expected total: $239.89

Products in cart: 9781430219484 x 3, 9780132071482
Expected total: $177.36

Products in cart: 9325336130810, 9325336028278, 9780201835953
Expected total: $71.36


Example interface:

$cart = new Cart($pricingRules);
$cart->addProduct("9780201835953");
$cart->addProduct("9781430219484");
$cart->total();


* use any language you wish
* do not use any external libraries (Zend etc.)
* do not use a database
* do not create a GUI (we are only interested in your implementation)
* try not to spend more than two hours on this, we don't want you working all day!




ASSUMPTION
==========
I have assumed that when customer buys game of thrones: season 1, they will automaitcally get 1 free fresh prince of bell air.

case 1:  if customer has game of throne and fresh prince in their cart then fresh prince will be free
case 2 if customer has game of throne and 2 items fresh prince in their cart then 1 fresh prince will be free and they will be charged for the second.

case 3: if cuastomer only buys game of thrones, then they will get fresh prince added to cart free of charge.


