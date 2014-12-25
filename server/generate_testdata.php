<?
include("DBManager.php");

function generateInsert($tablename, $columnlist, $data_arr)
{
	$sql = array();
	for ($i=0;$i<count($data_arr);$i++)
	{
		$sql[$i] = "INSERT INTO $tablename ($columnlist) VALUES (";
		foreach ($data_arr[$i] as $data)
		{
			$sql[$i] .= "$data,";
		}
		$sql[$i] = substr($sql[$i],0,-1);
		$sql[$i] .= ')';
	}
	return $sql;
}

function submitData($input_arr, $stepname)
{
	$DBManager = new DBManager();
	$DBManager->beginSet();
	for ($i=0;$i<count($input_arr);$i++)
	{
		$set_data[$i] = $DBManager->setData($input_arr[$i]);
	}

	$result = true;
	foreach ($set_data as $set)
	{
		$result = $result && $set;
	}

	if($result)
	{
		$DBManager->commitWork();
		echo "<font color=green>$stepname [success]</font><br>";
	}
	else
	{
		$DBManager->rollbackWork();
		echo "<font color=red>$stepname [fail]</font><br>";
	}	
}

//1. clear all data from database
$sql_droptable[0] = "DROP table subject_semester_classof";
$sql_droptable[1] = "DROP table subject";
$sql_droptable[2] = "DROP table student";
$sql_droptable[3] = "DROP table semester";
$sql_droptable[4] = "DROP table classof";
$sql_droptable[5] = "DROP table enrollment";

submitData($sql_droptable, "Step1: Drop table");


//2. Create table
$sql_createtable[0] = "CREATE TABLE `classof` (
`classof_id` int(4) NOT NULL,
  `classof_description` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sql_createtable[1] = "CREATE TABLE `enrollment` (
  `user_id` int(10) NOT NULL,
  `subject_id` int(8) NOT NULL,
  `semester_id` int(4) NOT NULL,
  `status` int(2) NOT NULL,
  `modification_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sql_createtable[2] = "CREATE TABLE `semester` (
`semester_id` int(4) NOT NULL,
  `semester_description` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sql_createtable[3] = "CREATE TABLE `student` (
  `user_id` int(10) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `classof_id` int(4) NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `GPA` float NOT NULL,
  `modification_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sql_createtable[4] = "CREATE TABLE `subject` (
  `subject_id` int(8) NOT NULL,
  `subject_title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `subject_description` text COLLATE utf8_unicode_ci NOT NULL,
  `modification_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sql_createtable[5] = "CREATE TABLE `subject_semester_classof` (
  `subject_id` int(8) NOT NULL,
  `semester_id` int(4) NOT NULL,
  `classof_id` int(4) NOT NULL,
  `maxstudent` int(5) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `modification_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sql_createtable[6] = "ALTER TABLE `classof`
 ADD PRIMARY KEY (`classof_id`);";

 $sql_createtable[7] = "ALTER TABLE `enrollment`
 ADD PRIMARY KEY (`user_id`,`subject_id`,`semester_id`);";

 $sql_createtable[8] = "ALTER TABLE `semester`
 ADD PRIMARY KEY (`semester_id`);";

 $sql_createtable[9] = "ALTER TABLE `student`
 ADD PRIMARY KEY (`user_id`);";

 $sql_createtable[10] = "ALTER TABLE `subject`
 ADD PRIMARY KEY (`subject_id`);";

 $sql_createtable[11] = "ALTER TABLE `subject_semester_classof`
 ADD PRIMARY KEY (`subject_id`,`semester_id`,`classof_id`);";

$sql_createtable[12] = "ALTER TABLE `classof`
MODIFY `classof_id` int(4) NOT NULL AUTO_INCREMENT;";

$sql_createtable[13] = "ALTER TABLE `semester`
MODIFY `semester_id` int(4) NOT NULL AUTO_INCREMENT;";

$sql_createtable[14] = "ALTER TABLE `enrollment` CHANGE `status` `status` INT(2) NOT NULL DEFAULT '0';";

submitData($sql_createtable, "Step2: create table");

//3. insert new data into database

$classof_arr = array(
		0 => array("'class of 2557/2 วันธรรมดา'"),
		1 => array("'class of 2557/2 วันเสาร์อาทิตย์'")
	);

$semester_arr = array(
		0 => array("'2557 เทอม 1'"),
		1 => array("'2557 เทอม 2'"),
		2 => array("'2557 เทอม 3'")
	);

$subject_semester_classof_arr = array(
		0 => array(1000000, 0, 0, 20, "'desc 1'", "localtime()"),
		0 => array(1000001, 0, 0, 20, "'desc 2'", "localtime()"),
		0 => array(1000002, 0, 0, 20, "'desc 3'", "localtime()"),
		0 => array(1000003, 0, 0, 20, "'desc 4'", "localtime()"),
		0 => array(1000004, 0, 0, 20, "'desc 5'", "localtime()"),
	);

$subject_arr = array(
		0 => array(1000000, "'subject A'", "'subject A description'", "localtime()"),
		1 => array(1000001, "'subject B'", "'subject B description'", "localtime()"),
		2 => array(1000002, "'subject C'", "'subject C description'", "localtime()"),
		3 => array(1000003, "'subject D'", "'subject D description'", "localtime()"),
		4 => array(1000004, "'subject E'", "'subject E description'", "localtime()")
	);

$student_arr = array(
		0 => array(20000000,"'User 1'","'Lastname 1'","'0'","'user@user.com1'",3.00,"localtime()"),
		1 => array(20000001,"'User 2'","'Lastname 2'","'0'","'user@user.com2'",3.00,"localtime()"),
		2 => array(20000002,"'User 3'","'Lastname 3'","'0'","'user@user.com3'",3.00,"localtime()"),
		3 => array(20000003,"'User 4'","'Lastname 4'","'0'","'user@user.com4'",3.00,"localtime()"),
		4 => array(20000004,"'User 5'","'Lastname 5'","'0'","'user@user.com5'",3.00,"localtime()"),
		5 => array(20000005,"'User 6'","'Lastname 6'","'0'","'user@user.com6'",3.00,"localtime()"),
		6 => array(20000006,"'User 7'","'Lastname 7'","'0'","'user@user.com7'",3.00,"localtime()"),
		7 => array(20000007,"'User 8'","'Lastname 8'","'0'","'user@user.com8'",3.00,"localtime()"),
		8 => array(20000008,"'User 9'","'Lastname 9'","'0'","'user@user.com9'",3.00,"localtime()"),
		9 => array(20000009,"'User 10'","'Lastname 10'","'0'","'user@user.com10'",3.00,"localtime()"),
		10 => array(20000010,"'User 11'","'Lastname 11'","'0'","'user@user.com11'",3.10,"localtime()"),
		11 => array(20000011,"'User 12'","'Lastname 12'","'0'","'user@user.com12'",3.11,"localtime()"),
		12 => array(20000012,"'User 13'","'Lastname 13'","'0'","'user@user.com13'",3.12,"localtime()"),
		13 => array(20000013,"'User 14'","'Lastname 14'","'0'","'user@user.com14'",3.13,"localtime()"),
		14 => array(20000014,"'User 15'","'Lastname 15'","'0'","'user@user.com15'",3.14,"localtime()"),
		15 => array(20000015,"'User 16'","'Lastname 16'","'0'","'user@user.com16'",3.15,"localtime()"),
		16 => array(20000016,"'User 17'","'Lastname 17'","'0'","'user@user.com17'",3.16,"localtime()"),
		17 => array(20000017,"'User 18'","'Lastname 18'","'0'","'user@user.com18'",3.17,"localtime()"),
		18 => array(20000018,"'User 19'","'Lastname 19'","'0'","'user@user.com19'",3.18,"localtime()"),
		19 => array(20000019,"'User 20'","'Lastname 20'","'0'","'user@user.com20'",3.19,"localtime()"),
		20 => array(20000020,"'User 21'","'Lastname 21'","'0'","'user@user.com21'",3.20,"localtime()"),
		21 => array(20000021,"'User 22'","'Lastname 22'","'0'","'user@user.com22'",3.21,"localtime()"),
		22 => array(20000022,"'User 23'","'Lastname 23'","'0'","'user@user.com23'",3.22,"localtime()"),
		23 => array(20000023,"'User 24'","'Lastname 24'","'0'","'user@user.com24'",3.23,"localtime()"),
		24 => array(20000024,"'User 25'","'Lastname 25'","'0'","'user@user.com25'",3.24,"localtime()"),
		25 => array(20000025,"'User 26'","'Lastname 26'","'0'","'user@user.com26'",3.25,"localtime()"),
		26 => array(20000026,"'User 27'","'Lastname 27'","'0'","'user@user.com27'",3.26,"localtime()"),
		27 => array(20000027,"'User 28'","'Lastname 28'","'0'","'user@user.com28'",3.27,"localtime()"),
		28 => array(20000028,"'User 29'","'Lastname 29'","'0'","'user@user.com29'",3.28,"localtime()"),
		29 => array(20000029,"'User 30'","'Lastname 30'","'0'","'user@user.com30'",3.29,"localtime()"),
		30 => array(20000030,"'User 31'","'Lastname 31'","'0'","'user@user.com31'",3.30,"localtime()"),
		31 => array(20000031,"'User 32'","'Lastname 32'","'0'","'user@user.com32'",3.31,"localtime()"),
		32 => array(20000032,"'User 33'","'Lastname 33'","'0'","'user@user.com33'",3.32,"localtime()"),
		33 => array(20000033,"'User 34'","'Lastname 34'","'0'","'user@user.com34'",3.33,"localtime()"),
		34 => array(20000034,"'User 35'","'Lastname 35'","'0'","'user@user.com35'",3.34,"localtime()"),
		35 => array(20000035,"'User 36'","'Lastname 36'","'0'","'user@user.com36'",3.35,"localtime()"),
		36 => array(20000036,"'User 37'","'Lastname 37'","'0'","'user@user.com37'",3.36,"localtime()"),
		37 => array(20000037,"'User 38'","'Lastname 38'","'0'","'user@user.com38'",3.37,"localtime()"),
		38 => array(20000038,"'User 39'","'Lastname 39'","'0'","'user@user.com39'",3.38,"localtime()"),
		39 => array(20000039,"'User 40'","'Lastname 40'","'0'","'user@user.com40'",3.39,"localtime()"),
		40 => array(20000040,"'User 41'","'Lastname 41'","'0'","'user@user.com41'",3.40,"localtime()"),
		41 => array(20000041,"'User 42'","'Lastname 42'","'0'","'user@user.com42'",3.41,"localtime()"),
		42 => array(20000042,"'User 43'","'Lastname 43'","'0'","'user@user.com43'",3.42,"localtime()"),
		43 => array(20000043,"'User 44'","'Lastname 44'","'0'","'user@user.com44'",3.43,"localtime()"),
		44 => array(20000044,"'User 45'","'Lastname 45'","'0'","'user@user.com45'",3.44,"localtime()"),
		45 => array(20000045,"'User 46'","'Lastname 46'","'0'","'user@user.com46'",3.45,"localtime()"),
		46 => array(20000046,"'User 47'","'Lastname 47'","'0'","'user@user.com47'",3.46,"localtime()"),
		47 => array(20000047,"'User 48'","'Lastname 48'","'0'","'user@user.com48'",3.47,"localtime()"),
		48 => array(20000048,"'User 49'","'Lastname 49'","'0'","'user@user.com49'",3.48,"localtime()"),
		49 => array(20000049,"'User 50'","'Lastname 50'","'0'","'user@user.com50'",3.49,"localtime()"),
		50 => array(20000050,"'User 51'","'Lastname 51'","'0'","'user@user.com51'",3.50,"localtime()"),
		51 => array(20000051,"'User 52'","'Lastname 52'","'0'","'user@user.com52'",3.51,"localtime()"),
		52 => array(20000052,"'User 53'","'Lastname 53'","'0'","'user@user.com53'",3.52,"localtime()"),
		53 => array(20000053,"'User 54'","'Lastname 54'","'0'","'user@user.com54'",3.53,"localtime()"),
		54 => array(20000054,"'User 55'","'Lastname 55'","'0'","'user@user.com55'",3.54,"localtime()"),
		55 => array(20000055,"'User 56'","'Lastname 56'","'0'","'user@user.com56'",3.55,"localtime()"),
		56 => array(20000056,"'User 57'","'Lastname 57'","'0'","'user@user.com57'",3.56,"localtime()"),
		57 => array(20000057,"'User 58'","'Lastname 58'","'0'","'user@user.com58'",3.57,"localtime()"),
		58 => array(20000058,"'User 59'","'Lastname 59'","'0'","'user@user.com59'",3.58,"localtime()"),
		59 => array(20000059,"'User 60'","'Lastname 60'","'0'","'user@user.com60'",3.59,"localtime()"),
		60 => array(20000060,"'User 61'","'Lastname 61'","'0'","'user@user.com61'",3.60,"localtime()"),
		61 => array(20000061,"'User 62'","'Lastname 62'","'0'","'user@user.com62'",3.61,"localtime()"),
		62 => array(20000062,"'User 63'","'Lastname 63'","'0'","'user@user.com63'",3.62,"localtime()"),
		63 => array(20000063,"'User 64'","'Lastname 64'","'0'","'user@user.com64'",3.63,"localtime()"),
		64 => array(20000064,"'User 65'","'Lastname 65'","'0'","'user@user.com65'",3.64,"localtime()"),
		65 => array(20000065,"'User 66'","'Lastname 66'","'0'","'user@user.com66'",3.65,"localtime()"),
	);

$enrollment_arr = array(
		0 => array(20000000,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		1 => array(20000001,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		2 => array(20000002,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		3 => array(20000003,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		4 => array(20000004,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		5 => array(20000005,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		6 => array(20000006,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		7 => array(20000007,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		8 => array(20000008,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		9 => array(20000009,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		10 => array(20000010,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		11 => array(20000011,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		12 => array(20000012,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		13 => array(20000013,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		14 => array(20000014,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		15 => array(20000015,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		16 => array(20000016,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		17 => array(20000017,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		18 => array(20000018,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		19 => array(20000019,1000000,0,"localtime()","UNIX_TIMESTAMP()"),
		20 => array(20000020,1000000,0,"localtime()+1","UNIX_TIMESTAMP()+1"),
		21 => array(20000021,1000000,0,"localtime()+2","UNIX_TIMESTAMP()+2"),
		22 => array(20000022,1000000,0,"localtime()+3","UNIX_TIMESTAMP()+3"),
		23 => array(20000023,1000000,0,"localtime()+4","UNIX_TIMESTAMP()+4"),
		24 => array(20000024,1000000,0,"localtime()+5","UNIX_TIMESTAMP()+5"),
		25 => array(20000025,1000000,0,"localtime()+6","UNIX_TIMESTAMP()+6"),
		26 => array(20000026,1000000,0,"localtime()+7","UNIX_TIMESTAMP()+7"),
		27 => array(20000027,1000000,0,"localtime()+8","UNIX_TIMESTAMP()+8"),
		28 => array(20000028,1000000,0,"localtime()+9","UNIX_TIMESTAMP()+9"),
		29 => array(20000029,1000000,0,"localtime()+10","UNIX_TIMESTAMP()+10"),
		30 => array(20000030,1000000,0,"localtime()+11","UNIX_TIMESTAMP()+11"),
		31 => array(20000031,1000000,0,"localtime()+12","UNIX_TIMESTAMP()+12"),
		32 => array(20000032,1000000,0,"localtime()+13","UNIX_TIMESTAMP()+13"),
		33 => array(20000033,1000000,0,"localtime()+14","UNIX_TIMESTAMP()+14"),
		34 => array(20000034,1000000,0,"localtime()+15","UNIX_TIMESTAMP()+15"),
		35 => array(20000035,1000000,0,"localtime()+16","UNIX_TIMESTAMP()+16"),
		36 => array(20000036,1000000,0,"localtime()+17","UNIX_TIMESTAMP()+17"),
		37 => array(20000037,1000000,0,"localtime()+18","UNIX_TIMESTAMP()+18"),
		38 => array(20000038,1000000,0,"localtime()+19","UNIX_TIMESTAMP()+19"),
		39 => array(20000039,1000000,0,"localtime()+20","UNIX_TIMESTAMP()+20"),
		40 => array(20000040,1000000,0,"localtime()+21","UNIX_TIMESTAMP()+21")
	);

$sql_data[0] = generateInsert("subject", "subject_id, subject_title, subject_description, modification_date", $subject_arr);
$sql_data[1] = generateInsert("student", "user_id, name, lastname, classof_id, email, GPA, modification_date", $student_arr);
$sql_data[2] = generateInsert("classof", "classof_description", $classof_arr);
$sql_data[3] = generateInsert("semester", "semester_description", $semester_arr);
$sql_data[4] = generateInsert("subject_semester_classof", "subject_id, semester_id, classof_id, maxstudent, description, modification_date", $subject_semester_classof_arr);
$sql_data[5] = generateInsert("enrollment", "user_id, subject_id, semester_id, modification_date, timestamp", $enrollment_arr);

for ($i=0;$i<count($sql_data);$i++)
{
	submitData($sql_data[$i], "Step3: prepare data - $i");	
}


?>