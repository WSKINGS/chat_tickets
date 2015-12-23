<?php

include "test_func.php";

$Activity = new Activity();

var_d($Activity);

##set_activity

$Activity->set_activity(1);

var_d($Activity);

##set_payment

// var_d($Activity->set_payment());

// var_d($Activity);

$order = new Order();
$order->set_order(6);

echo $Activity->get_payment()->pay($order, $Activity);