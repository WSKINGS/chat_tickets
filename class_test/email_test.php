<?php

include "test_func.php";

$email = new Email('TEST');

$ret = $email->send_template_mail('test', array(), 'dongxuli23@gmail.com');

var_d($ret);