<?php
/**
 * Created by PhpStorm.
 * User: mydn
 * Date: 2018/8/14
 * Time: 下午7:13
 */
require 'db.php';
class Model extends DB
{
	private $tableName;

	public function __construct($tablename)
	{
		$this->tableName = $tablename;
		parent::__construct();
	}

	private function _handlWhere($where)
	{
		$whereArr = array();
		foreach ($where as $k => $v){
			if (is_array($v)) {
				$temp_v = isset($v[0]) ? $v[0] : '';
				switch ($temp_v) {
					case 'in':
						$temp_v1 = isset($v[1]) ? $v[1] : array();
						$inStr = implode(',', $temp_v1);

						if (isset($temp_v1[0]) && !is_numeric($temp_v1[0])) {
							$inStr = str_replace(',', "','", $inStr);
							$inStr = "'". $inStr ."'";
						}

						$str = "{$k} in({$inStr})";

						break;

					case 'between':
						$str = "{$k} between '{$v[1][0]}' and '{$v[1][1]}'";

						break;
				}

				array_push($whereArr, $str);
				continue;
			}


			if (is_numeric($v)) {
				$str = $k ." = ". $v;
			} elseif(is_string($v)) {
				$str = $k ." = '". $v ."'";
			}

			array_push($whereArr, $str);
		}

		$whereStr = '';
		if ($whereArr) {
			$whereStr = " where ".implode(' and ', $whereArr);
		}

		return $whereStr;
	}

	public function doFind($where=[])
	{

		$whereStr = $this->_handlWhere($where);
//var_dump($whereStr);die;
		$sql = "select * from {$this->tableName}".$whereStr;

		return $this->find($sql);
	}

	public function doSelect($where=[], $orderby='')
	{

		$whereStr = $this->_handlWhere($where);

		if ($orderby) {
			$whereStr = $whereStr.' order by '.$orderby;
		}
		$sql = "select * from {$this->tableName}".$whereStr;

		return $this->select($sql);
	}

	public function doUpdate($where, $updateData)
	{
		$whereStr = $this->_handlWhere($where);

		$setData = array();
		foreach ($updateData as $k => $v){
			if (is_numeric($v)) {
				$str = $k ." = ". $v;
			} elseif(is_string($v)) {
				$str = $k ." = '". $v ."'";
			}

			array_push($setData, $str);
		}
		$setStr = implode(' and ', $setData);

		$sql = "update {$this->tableName} set ". $setStr . $whereStr;

		return $this->update($sql);
	}

	public function doAdd($addData)
	{
		$k_arr = array();
		$v_arr = array();
		foreach ($addData as $k => $v){
			if (is_numeric($v)) {
				$str = $v;
			} elseif(is_string($v)) {
				$str = "'". $v ."'";
			} else {
				continue;
			}

			array_push($k_arr, $k);
			array_push($v_arr, $str);
		}
		$k_str = implode(',', $k_arr);
		$v_str = implode(',', $v_arr);

		if ($k_str) {
			$sql = "insert into {$this->tableName} ({$k_str}) values ({$v_str})";
			return $this->add($sql);
		} else {
			return 0;
		}
	}

	public function doAddAll($list)
	{
		$k_arr = array();
		$v_arr = array();

		$num = count($list);
		foreach ($list as $key=>$item) {
			ksort($item);

			// 多维数组中的每个数组下标key必需相同，数量必需相同
			if ($num > $key+1) {
				if (array_diff_key($item, $list[$key+1]) || array_diff_key($list[$key+1], $item) || count($item) != count($list[$key+1])) {
					return 0;
				}
			}

			$temp_v_array = array();
			foreach ($item as $k => $v) {

				// 获取key
				if ($key < 1) {
					array_push($k_arr, $k);
				}

				// 获取value
				if (is_numeric($v)) {
					$str = $v;
				} elseif(is_string($v)) {
					$str = "'". $v ."'";
				} else {
					continue;
				}
				array_push($temp_v_array, $str);
			}

			$temp_val = "(". implode(',', $temp_v_array) .")";
			array_push($v_arr, $temp_val);

		}

		$k_str = implode(',', $k_arr);
		$v_str = implode(',', $v_arr);

		if ($k_str) {
			$sql = "insert into {$this->tableName} ({$k_str}) values {$v_str}";
			return $this->add($sql);
		} else {
			return 0;
		}
	}
}