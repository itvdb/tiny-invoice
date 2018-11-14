<?php
/**
 * Transaction_Invoice_Send
 *
 * @author Gerry Demaret <gerry@tigron.be>
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */

class Transaction_Invoice_Send extends Transaction {

	/**
	 * Constructor
	 *
	 * @access public
	 * @param int $id
	 */
	public function __construct($id = null) {
		parent::__construct($id);
		$this->type = 'Invoice_Send';
	}

	/**
	 * Run
	 *
	 * @access public
	 */
	public function run() {
		$invoice = Invoice::get_by_id($this->data['id']);
		$invoice->send();
	}
}
