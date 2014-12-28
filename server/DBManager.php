<?
include("mysqlconfig.inc.php");
Class DBManager 
{
	var $ihost;
	var $iuser;
	var $ipasswd;
	var $idbname;
	var $con;

	function DBManager ()
	{
		global $host, $user, $passwd, $dbname;
		$this->ihost = $host;
		$this->iuser = $user;
		$this->ipasswd = $passwd;
		$this->idbname = $dbname;
	}

	function getData($sql)
	{
		$this->con = mysqli_connect($this->ihost,$this->iuser,$this->ipasswd);
		mysqli_select_db($this->con, $this->idbname);
		mysqli_query($this->con, "SET NAMES 'utf8'");
		mysqli_query($this->con, 'SET CHARACTER SET utf8');
		mysqli_query($this->con, 'SET collation_connection utf8_unicode_ci'); 
		$result = mysqli_query($this->con, $sql);
		mysqli_close($this->con);
		return $result;
	}

	function countRow($sql_result)
	{
		$this->con = mysqli_connect($this->ihost,$this->iuser,$this->ipasswd);
		mysqli_select_db($this->con, $this->idbname);
		mysqli_query($this->con, "SET NAMES 'utf8'");
		mysqli_query($this->con, 'SET CHARACTER SET utf8');
		mysqli_query($this->con, 'SET collation_connection utf8_unicode_ci'); 
		$result = mysqli_num_rows($sql_result);
		mysqli_close($this->con);
		return $result;
	}

	function setData($sql)
	{
		mysqli_query($this->con, "SET NAMES 'utf8'");
		mysqli_query($this->con, 'SET CHARACTER SET utf8');
		mysqli_query($this->con, 'SET collation_connection utf8_unicode_ci'); 
		$result = mysqli_query($this->con, $sql);
		return $result;
	}

	function beginSet()
	{		
		$this->con = mysqli_connect($this->ihost,$this->iuser,$this->ipasswd);
		mysqli_select_db($this->con, $this->idbname);
		mysqli_query($this->con, 'BEGIN'); 	
	}

	function rollbackWork()
	{
		mysqli_query($this->con, 'ROLLBACK'); 
		mysqli_close($this->con);
	}

	function commitWork()
	{
		mysqli_query($this->con, 'COMMIT'); 
		mysqli_close($this->con);
	}

}

?>