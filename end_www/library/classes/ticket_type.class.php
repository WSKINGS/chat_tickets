<?php
/*
 * Ticket Class
 * @author lidongxu @ 2014.5.28
 * 
 * 完成门票的生成、修改、计算等操作
 */

class Ticket_Type {
	protected $ticket_data;
	protected $discount_type;

	/*
	 * 构造函数
	 */
	public function __construct() {
		$this->ticket_data = array();
	}

	/*
	 * 析构函数
	 */
	public function __destruct() {}

	/*
	 * set_ticket_data
	 */
	public function set_ticket_data($id) {
		$ret = array();

		$ticket = model('wt_ticket_type')->get_one($id);

		if ($ticket) {
			$ticket['name'] = json_decode($ticket['name'], true);
			$ticket['description'] = json_decode($ticket['description'], true);
			$this->ticket_data = $ticket;

			$ret['r'] = 'ok';
		}
		else {
			$ret['r'] = 'error';
			$ret['r'] = '30001:Illegal attendee id.';
		}

		return $ret;
	}

	/*
	 * get_ticket_data
	 */
	public function get_ticket_data() {
		return $this->ticket_data;
	}

	/*
	 * get_tickets_by_order
	 */
	public function get_tickets_by_activity($activity_id) {
		$ret = array();

		$tickets = array();

		$ticket_list = model('wt_ticket_type')->get_list(array('activity_id'=>$activity_id, 'order'=>'order_id asc'));
		if (!$ticket_list) {
			$ret['r'] = 'error';
			$ret['msg'] = '50004:Ticket list get failed.';

			return $ret;
		}

		for ($i = 0; $i < count($ticket_list); $i++) {
			$tickets[$i] = new self();
			$tickets[$i]->set_ticket_data($ticket_list[$i]['type_id']);
		}

		$ret['r'] = 'ok';
		$ret['msg'] = $tickets;

		return $ret;
	}

	/*
	 * get_id
	 */
	public function get_id() {
		return $this->ticket_data['type_id'];
	}

	/*
	 * is_open
	 */
	public function is_open() {
		if ($this->ticket_data['publish'] == '-1' || $this->ticket_data['status'] != '1') {
			return false;
		}

		if (time() < $this->ticket_data['start_time'] || time() > $this->ticket_data['end_time']) {
			return false;
		}

		return true;
	}

	/*
	 * is_need_attendee_info
	 */
	public function is_need_attendee_info() {
		if ($this->ticket_data['need_attendee_info'] == '1') {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * get_amount
	 */
	public function get_amount($quantity) {
		$amount = floatval($this->ticket_data['price'])*intval($quantity);

		return $amount;
	}

	/*
	 * check_ticket
	 */
	public function check_ticket($quantity = 0) {
		$ret = array();

		if (!$this->is_open()) {
			$ret['r'] = 'error';
			$ret['msg'] = '60001:Illegal tickets.';

			return $ret;
		}

		if ( $this->ticket_data['gross'] != '0' 
			&& $quantity > ($this->ticket_data['gross']-$this->ticket_data['booked_gross'])) {
			$ret['r'] = 'error';
			$ret['msg'] = '60002:Tickets is not enough.';

			return $ret;
		}

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * get_end_time
	 */
	public function get_end_time() {
		return $this->ticket_data['end_time'];
	}

	/*
	 * check_channel
	 */
	public function check_channel($channel) {
		if ($channel == $this->ticket_data['channel']) {
			return true;
		}
		else {
			return false;
		}
	}
}