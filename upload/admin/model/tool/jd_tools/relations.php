<?php
// v0.1.4
class ModelToolJdToolsRelations extends Model {

	public function getRelationKey( $key, $value, $source) {
		$sql = "SELECT `new_value` FROM `" . DB_PREFIX . "relations` WHERE `key` = '" . $key . "' AND `old_value` = '" . $this->db->escape($value) . "' AND `data_source` = '" . $source . "'";
		$result = $this->db->query($sql);
		//echo "KEY = $key, VALUE = $value <pre>"; print_r($result); echo "</pre>";

		if($result->num_rows > 1) {
			// Не унікальне значення! - це не припустимо. Повідомлення про помилку і вихід!.
			// todo jd Діагностувати ситуацію і виправити

			// UPD. 23-06-2020
			/*  Цей варіант можливий, коли в товарній групі є лише одна позиція-варіант.
			 *  Вона не переноситься в опції, а залишаєтся в атрибутах, при цьому не створюючи приставки-ключа опції.
			 *  Таким чином, виходить 2 prom_id до одного чистого product_id - допустимий варіант.
			 *  Коли з'явиться другий варіант - товар попаде в опцію
			 * */

			$msg = "Конфлікт унікального id. Дубль ключа! Повідомте програміста і надайте ці дані: " . print_r(
					[
						'file'  =>  __FILE__ . '::' . __LINE__,
						'method'    =>  'getRelationKey',
						'key'   => $key,
						'value' => $value,
						'source'    =>  $source,
						'result'    =>  print_r($result, 1),
					],
					1);
//			echo $msg;
			if(defined('MYLOG_ON') && MYLOG_ON) $this->log->write($msg);
//			die;
		}
		elseif ( $result->num_rows == 1 ) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "relations` WHERE `new_value` = '{$result->row['new_value']}' AND `key` = '{$key}' AND `data_source` = '{$source}'";
			$check = $this->db->query($sql);

			if($check->num_rows > 1) {
				// Не унікальне значення! - це не припустимо. Повідомлення про помилку і вихід!.
				// todo jd Діагностувати ситуацію і виправити

				$msg = "Конфлікт унікального id. Дубль ключа! Повідомте програміста і надайте ці дані: " . print_r(
						[
							'file'  =>  __FILE__ . "::" . __LINE__,
							'method'    =>  'getRelationKey',
							'key'   => $key,
							'value' => $value,
							'source'    =>  $source,
							'check'    =>  print_r($check, 1),
						],
						1);
//				echo $msg;
				if(defined('MYLOG_ON') && MYLOG_ON) $this->log->write($msg);
//				die;
			}
		}
		if ( $result->num_rows > 0 ) {
			//$this->printMyNode($result,'result from db query getNewKeyValue');
			return $result->row['new_value'];
		}
		else {
			return false;
		}
	}

	public function getRelationKeyFullData($key, $value, $source) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "relations` WHERE `key` = '" . $key . "' AND `old_value` = '" . $value . "' AND `data_source` = '" . $source . "'";
		$result = $this->db->query($sql);

		if ( $result->num_rows > 0 ) {
			return $result->row;
		}
		else {
			return false;
		}
	}

	public function getLastMode( $key, $value, $source) {
		$sql = "SELECT `lastmode` FROM `" . DB_PREFIX . "relations` WHERE `key` = '" . $key . "' AND `old_value` = '" . $value . "' AND `data_source` = '" . $source . "'";
		$result = $this->db->query($sql);

		return $result->row['lastmode'];
	}

	public function setRelationKey( $key, $old_value, $new_value, $source, $lastmode) {
		$check = "SELECT * FROM `" . DB_PREFIX. "relations`"
			. " WHERE `key` = '{$key}'"
			. " AND `old_value` = '" . $this->db->escape($old_value) . "'"
			. " AND `new_value` = '" . $this->db->escape($new_value) . "'"
			. " AND `data_source` = '" . $this->db->escape($source) . "'"
			. ";";
		$check = $this->db->query($check)->row;
		if(empty($check)) {
			$sql = "INSERT `" . DB_PREFIX. "relations` (`key`, `old_value`, `new_value`, `data_source`, `lastmode`)"
				. " VALUE ('" . $key ."', '". $old_value ."', '". $new_value ."', '" . $source . "', " . (($lastmode == '')? 'NOW()' : "'" . $lastmode . "'" ) . ")"
				. " ON DUPLICATE KEY UPDATE `new_value` = '" . $new_value . "',"
				. " `lastmode` = " . (($lastmode == '')? 'NOW()' : "'" . $lastmode . "'") . ";";
			$result = $this->db->query($sql);
		}
		else {
			$result = $check['new_value'];
			/*
			$this->log->write("Дубль значення в setRelationKey!\r\n" . print_r(
				[
					'key' => $key,
					'old_value' => $old_value,
					'new_value' => $new_value,
					'data_source'   => $source
				], 1
			));
			*/
		}

		return $result;
	}

	public function updateRelationValue($key, $source_value, $new_value, $source) {
		$sql = "UPDATE `" . DB_PREFIX . "relations` SET `new_value` = '{$new_value}' WHERE `data_source` = '{$source}' AND `key` = '{$key}' AND `old_value` = '{$source_value}'";
		$result = $this->db->query($sql);
		return $result;
	}

	public function createRelationsTable() {
		$q = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "relations` "
				. "(`data_source` CHAR(50) NOT NULL, "
				. "`key` CHAR(50) NOT NULL, "
				. "`old_value` CHAR(50) NOT NULL, "
				. "`new_value` CHAR(50) NOT NULL, "
				. "`lastmode` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, "
				. "UNIQUE INDEX `data_source_key_old_value_new_value` (`data_source`, `key`, `old_value`, `new_value`) "
				. ") COLLATE='utf8_general_ci' ENGINE=MyISAM;";
		$result = $this->db->query($q);

		return $result;
	}
}