<?php

include "test_func.php";

$Ticket_Type = new Ticket_Type();

var_d($Ticket_Type);

//var_d($Ticket_Type->get_tickets_by_activity(1));

$Ticket_Type->set_ticket_data(1);

var_d($Ticket_Type);