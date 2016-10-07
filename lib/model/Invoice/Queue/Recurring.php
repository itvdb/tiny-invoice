<?php
/**
 * Invoice_Queue_Recurring class
 *
 * @package KNX-lib
 * @author Gerry Demaret <gerry@tigron.be>
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */

use \Skeleton\Database\Database;

class Invoice_Queue_Recurring {
	use \Skeleton\Object\Model;
	use \Skeleton\Object\Get;
	use \Skeleton\Object\Save;
	use \Skeleton\Object\Delete;

	/**
	 * Validates the given input before insertion
	 *
	 * @param array The array that will contain the errors encountered. Passed by reference.
	 * @return array The array containing the errors
	 * @access public
	 */
	public function validate(&$errors = []) {
		$config = Config::Get();

		$required_fields = array('name', 'price');

		$errors = array();
		foreach ($required_fields as $field) {
			if (!isset($this->details[$field]) || $this->details[$field] == '') {
				$errors[$field] = $field;
			}
		}
		if (count($errors) > 0) {
			return $errors;
		}
	}

	/**
	 * Run
	 *
	 * @access public
	 */
	public function run() {
		$invoice_queue = new Invoice_Queue();
		$invoice_queue->customer_contact_id = $this->invoice_queue_recurring_group->customer_contact_id;

		$customer_contact = $invoice_queue->customer_contact;

		$invoice_queue->customer_id = $this->invoice_queue_recurring_group->customer_id;
		$invoice_queue->product_type_id = $this->product_type_id;

		$description = $this->description;

		$next_run = strtotime($this->invoice_queue_recurring_group->next_run);

		$name = str_replace('%%day%%', date('d', $next_run), $description);
		$name = str_replace('%%month%%', date('m', $next_run), $description);
		$name = str_replace('%%year%%', date('Y', $next_run), $description);
		$name = str_replace('%%period_start%%', date('d-m-Y', $next_run), $description);
		$name = str_replace('%%period_end%%', date('d-m-Y', strtotime($this->invoice_queue_recurring_group->repeat_every, $next_run)), $description);
		$name = str_replace('%%day_name%%', date('l', $next_run), $description);
		$name = str_replace('%%month_name%%', date('F', $next_run), $description);

		$invoice_queue->description = $description;
		$invoice_queue->price = $this->price;
		$invoice_queue->qty = $this->qty;

		$vat = $customer_contact->get_vat(Vat_Rate::get_by_id(1));
		$invoice_queue->vat = $vat;

		$invoice_queue->save();

		$history = new Invoice_Queue_Recurring_History();
		$history->invoice_queue_recurring_id = $this->id;
		$history->invoice_queue_id = $invoice_queue->id;
		$history->save();
	}

	/**
	 * Get by Invoice_Queue_Recurring_Group
	 *
	 * @access public
	 * @param Invoice_Queue_Recurring_Group $group
	 * @return array $invoice_queue_recurring
	 */
	public static function get_by_invoice_queue_recurring_group(Invoice_Queue_Recurring_Group $group) {
		$db = Database::Get();
		$ids = $db->get_column('SELECT id FROM invoice_queue_recurring WHERE invoice_queue_recurring_group_id=? AND archived="0000-00-00 00:00:00"', array($group->id));
		$items = array();
		foreach ($ids as $id) {
			$items[] = Invoice_Queue_Recurring::get_by_id($id);
		}
		return $items;
	}
}