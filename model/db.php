<?php
/**
 * Created by PhpStorm.
 * User: mydn
 * Date: 2018/8/14
 * Time: 下午4:48
 */

class DB
{
	private $host;
	private $port;
	private $dbName;
	private $userName;
	private $password;
	private $mysqli;
	private $charset;
	private $lastSQL;

	public function __construct()
	{
		$array = require_once '../config/databases.php';
		$this->charset = isset($array['charset']) ? $array['charset'] : 'utf8';
		$this->connect($array);
		$this->mysqlConnect();
	}

	public function connect($array)
	{
		$this->host = isset($array['hostname']) ? $array['hostname'] : '127.0.0.1';
		$this->userName = isset($array['username']) ? $array['username'] : 'root';
		$this->password = isset($array['password']) ? $array['password'] : 'root';
		$this->dbName = isset($array['database']) ? $array['database'] : '';
		$this->port = isset($array['hostport']) ? $array['hostport'] : '3306';

	}

	private function mysqlConnect()
	{
		$this->mysqli = new mysqli($this->host, $this->userName, $this->password, $this->dbName, $this->port);

	}

	private function query($sql)
	{
		$this->lastSQL = $sql;

		$result = $this->mysqli->query($this->lastSQL);

		if($result === false)
		{
			$this->error();
		}

		return $result;
	}

	private function error()
	{
		$result = '';
		if ($this->mysqli->error)
		{
			$result .= "错误提示：{$this->mysqli->error}<br />";
		}

		if ($this->mysqli->errno)
		{
			$result .= "错误代号：{$this->mysqli->errno}<br />";
		}

		if ($result)
		{
			$result .= "错误的sql语句：{$this->lastSQL}<br />";
		}

		if ($result)
		{
			exit($result);
		} else {
			exit('没有错误');
		}
	}

	public function getLastSql()
	{
		return $this->lastSQL;
	}

	public function find($sql)
	{
		$result = $this->query($sql);

		$list = '';
		if ($result->num_rows > 0)
		{
			$list = $result->fetch_all(MYSQLI_ASSOC);
			$list = $list ? $list[0] : '';
		}
		return $list;
	}

	public function select($sql)
	{
		$result = $this->query($sql);

		$list = array();
		if ($result->num_rows > 0)
		{
			$list = $result->fetch_all(MYSQLI_ASSOC);
			$list = $list ? $list : array();
		}
		return $list;
	}

	public function update($sql)
	{
		$this->query($sql);
		return $this->mysqli->affected_rows;
	}

	public function add($sql)
	{
		$this->query($sql);
		return $this->mysqli->affected_rows;
	}


}