<?php
/**
 * Invoice_Item class
 *
 * @author David Vandemaele <david@tigron.be>
 */

class Invoice_Item {
	use Get, Model, Save;

	/**
	 * Validate data
	 *
	 * @access public
	 * @param array $errors
	 * @return bool $validated
	 */
	public function validate(&$errors = []) {
		$required_fields = array('description', 'qty', 'price');
		foreach ($required_fields as $required_field) {
			if (!isset($this->details[$required_field]) OR $this->details[$required_field] == '') {
				$errors[$required_field] = 'required';
			}
		}

		if (count($errors) > 0) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get price excl
	 *
	 * @access public
	 * @return decimal $price_excl
	 */
	public function get_price_excl() {
		return $this->price * $this->qty;
	}

	/**
	 * Get by invoice
	 *
	 * @access public
	 * @param Invoice $invoice
	 * @return array Invoice_Item $items
	 */
	public static function get_by_invoice(Invoice $invoice) {
		$table = self::trait_get_database_table();
		$db = self::trait_get_database();
		$ids = $db->getCol('SELECT id FROM ' . $table . ' WHERE invoice_id = ?', [ $invoice->id ]);

		$items = [];
		foreach ($ids as $id) {
			$items[] = self::get_by_id($id);
		}

		return $items;
	}
}
