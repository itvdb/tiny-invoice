<?php
/**
 * Country class
 *
 * @author David Vandemaele <david@tigron.be>
 */

class Country {
	use Get, Delete, Model, Save;

	/**
	 * Get by ISO2
	 *
	 * @access public
	 * @param string $iso2
	 * @return Country $country
	 */
	public static function get_by_iso2($iso2) {
		$db = Database::Get();
		$id = $db->getOne('SELECT id FROM country WHERE ISO2 = ?', [ $iso2 ]);

		if ($id === null) {
			throw new Exception('No such country');
		}

		return self::get_by_id($id);
	}

	/**
	 * Get grouped
	 *
	 * @access public
	 * @return array $countries
	 */
	public static function get_grouped() {
		$countries = [
			'european' => [],
			'rest' => []
		];

		$db = Database::Get();
		$ids = $db->getCol('SELECT id FROM country WHERE european = 1 ORDER BY name ASC', []);
		foreach ($ids as $id) {
			$countries['european'][] = self::get_by_id($id);
		}

		$ids = $db->getCol('SELECT id FROM country WHERE european = 0 ORDER BY name ASC', []);
		foreach ($ids as $id) {
			$countries['rest'][] = self::get_by_id($id);
		}
		return $countries;
	}
}
