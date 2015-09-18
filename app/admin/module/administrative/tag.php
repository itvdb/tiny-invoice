<?php
/**
 * Tag management Actions module
 *
 * @package KNX-Web-Admin
 * @subpackage modules
 * @author Gerry Demaret <gerry@tigron.be>
 * @author Christophe Gosiau <christophe@tigron.be>
 * @version $Id: user.php 529 2010-03-16 17:09:26Z knx-onlineshop $
 */

class Web_Module_Administrative_Tag extends Web_Module {

	/**
	 * Login required
	 *
	 * @var $login_required
	 */
	protected $login_required = true;

	/**
	 * Template
	 *
	 * @access protected
	 * @var string $template
	 */
	protected $template = 'administrative/tag.twig';

	/**
	 * Display
	 * This is the default method for a module.
	 *
	 * @access public
	 */
	public function display() {
		$pager = new Web_Pager('Tag');

		if (isset($_POST['search'])) {
			$pager->set_search($_POST['search']);
		}

		$pager->add_sort_permission('name');

		$pager->page();

		$template = Web_Template::Get();
		$template->assign('pager', $pager);
	}

	/**
	 * Display delete
	 * Deletes a user
	 *
	 * @access private
	 */
	protected function display_delete() {
		$tag = Tag::get_by_id($_GET['id']);
		$tag->delete();

		Web_Session::Redirect('/administrative/tag');
	}

	/**
	 * Display Add
	 * Adds a new user
	 *
	 * @access private
	 */
	protected function display_add() {
		$tag = new Tag();
		$tag->load_array($_POST['tag']);
		$tag->save();

		Web_Session::Redirect('/administrative/tag');
	}

	/**
	 * Display edit
	 * Display the detailed information of a user
	 *
	 * @access private
	 */
	public function display_edit() {
		$session = Web_Session_Sticky::Get();
		$template = Web_Template::Get();
		$tag = Tag::get_by_id($_GET['id']);

		if (isset($_POST['tag'])) {
			$tag->load_array($_POST['tag']);
			$tag->save();

			$session->message = 'tag_updated';

			Web_Session::Redirect('/administrative/tag?action=edit&id=' . $tag->id);
		}

		if (isset($session->message)) {
			$template->assign('session_message', $session->message);
		}
		$template->assign('tag', $tag);
	}

}
