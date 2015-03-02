<?php
include('sqlserverconfig.inc.php');
Class DBManager
{
	var $serverName;
	var $connectionInfo;
	var $conn;

	function DBManager()
	{
		global $host,$user,$passwd,$dbname,$servernames;
		$this->serverName = $servernames; //serverName\instanceName
		$this->connectionInfo = array( "Database"=>$dbname, "UID"=>$user, "PWD"=>$passwd, "CharacterSet" => "UTF-8");
		$this->conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
		if ($this->conn === false)
		{
			die( print_r( sqlsrv_errors(), true));
		}
	}
	
	function getData($sql)
	{
		$result = sqlsrv_query($this->conn, $sql);
		return $result;
	}

	function setData($sql, $params=array())
	{
		$result = sqlsrv_query($this->conn, $sql, $params);
		return $result;
	}

	function beginSet()
	{		
		sqlsrv_query($this->conn, 'BEGIN TRAN'); 	
	}

	function rollbackWork()
	{
		sqlsrv_query($this->conn, 'ROLLBACK'); 
	}

	function commitWork()
	{
		sqlsrv_query($this->conn, 'COMMIT'); 
	}

	function close()
	{
		sqlsrv_close($this->conn);
	}

	function errmsg()
	{
		return sqlsrv_errors();
	}

}
?>