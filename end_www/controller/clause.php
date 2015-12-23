<?php

include_once __DIR__."/ticket_include.php";

$view_data['title'] = 'Clause';

$clause = model('wt_clause')->get_one($activity_id);

$view_data['clause'] = $clause['clause_'.$lang];