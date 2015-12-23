<?php

include_once __DIR__."/ticket_include.php";

$view_data['title'] = 'Choose Ticket';

$tickets = $booking_activity->get_tickets();

$ticket_datas = array();

for ($i = 0; $i < count($tickets); $i++) {
	if ( $tickets[$i]->is_open() && $tickets[$i]->check_channel($channel) ) {
		$ticket_datas[$i] = $tickets[$i]->get_ticket_data();
		$ticket_datas[$i]['start_time'] = inttodate($ticket_datas[$i]['start_time']);
		$ticket_datas[$i]['end_time'] = inttodate($ticket_datas[$i]['end_time']);
	}
}

$view_data['tickets'] = $ticket_datas;

$clause = model('wt_clause')->get_one($activity_id);

$view_data['clause'] = $clause['clause_'.$lang];