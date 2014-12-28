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


//2. Create table
$sql_createtable[0] = "CREATE TABLE `classof` (
`classof_id` int(4) NOT NULL,
  `classof_description` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sql_createtable[1] = "CREATE TABLE `enrollment` (
  `user_id` int(10) NOT NULL,
  `subject_id` int(8) NOT NULL,
  `semester_id` int(4) NOT NULL,
  `rank` int(2) NOT NULL,
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
		0 => array(1000000, 0, 0, 40, "'desc 1'", "localtime()"),
		1 => array(1000001, 0, 0, 20, "'desc 2'", "localtime()"),
		2 => array(1000002, 0, 0, 20, "'desc 3'", "localtime()"),
		3 => array(1000003, 0, 0, 20, "'desc 4'", "localtime()"),
		4 => array(1000004, 0, 0, 20, "'desc 5'", "localtime()"),
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
		66 => array(20000066,"'User 67'","'Lastname 67'","'0'","'user@user.com67'",3.66,"localtime()"),
		67 => array(20000067,"'User 68'","'Lastname 68'","'0'","'user@user.com68'",3.67,"localtime()"),
		68 => array(20000068,"'User 69'","'Lastname 69'","'0'","'user@user.com69'",3.68,"localtime()"),
		69 => array(20000069,"'User 70'","'Lastname 70'","'0'","'user@user.com70'",3.69,"localtime()"),
		70 => array(20000070,"'User 71'","'Lastname 71'","'0'","'user@user.com71'",3.70,"localtime()"),
		71 => array(20000071,"'User 72'","'Lastname 72'","'0'","'user@user.com72'",3.71,"localtime()"),
		72 => array(20000072,"'User 73'","'Lastname 73'","'0'","'user@user.com73'",3.72,"localtime()"),
		73 => array(20000073,"'User 74'","'Lastname 74'","'0'","'user@user.com74'",3.73,"localtime()"),
		74 => array(20000074,"'User 75'","'Lastname 75'","'0'","'user@user.com75'",3.74,"localtime()"),
		75 => array(20000075,"'User 76'","'Lastname 76'","'0'","'user@user.com76'",3.75,"localtime()"),
		76 => array(20000076,"'User 77'","'Lastname 77'","'0'","'user@user.com77'",3.76,"localtime()"),
		77 => array(20000077,"'User 78'","'Lastname 78'","'0'","'user@user.com78'",3.77,"localtime()"),
		78 => array(20000078,"'User 79'","'Lastname 79'","'0'","'user@user.com79'",3.78,"localtime()"),
		79 => array(20000079,"'User 80'","'Lastname 80'","'0'","'user@user.com80'",3.79,"localtime()"),
		80 => array(20000080,"'User 81'","'Lastname 81'","'0'","'user@user.com81'",3.80,"localtime()"),
		81 => array(20000081,"'User 82'","'Lastname 82'","'0'","'user@user.com82'",3.81,"localtime()"),
		82 => array(20000082,"'User 83'","'Lastname 83'","'0'","'user@user.com83'",3.82,"localtime()"),
		83 => array(20000083,"'User 84'","'Lastname 84'","'0'","'user@user.com84'",3.83,"localtime()"),
		84 => array(20000084,"'User 85'","'Lastname 85'","'0'","'user@user.com85'",3.84,"localtime()"),
		85 => array(20000085,"'User 86'","'Lastname 86'","'0'","'user@user.com86'",3.85,"localtime()"),
		86 => array(20000086,"'User 87'","'Lastname 87'","'0'","'user@user.com87'",3.86,"localtime()"),
		87 => array(20000087,"'User 88'","'Lastname 88'","'0'","'user@user.com88'",3.87,"localtime()"),
		88 => array(20000088,"'User 89'","'Lastname 89'","'0'","'user@user.com89'",3.88,"localtime()"),
		89 => array(20000089,"'User 90'","'Lastname 90'","'0'","'user@user.com90'",3.89,"localtime()"),
		90 => array(20000090,"'User 91'","'Lastname 91'","'0'","'user@user.com91'",3.90,"localtime()"),
		91 => array(20000091,"'User 92'","'Lastname 92'","'0'","'user@user.com92'",3.91,"localtime()"),
		92 => array(20000092,"'User 93'","'Lastname 93'","'0'","'user@user.com93'",3.92,"localtime()"),
		93 => array(20000093,"'User 94'","'Lastname 94'","'0'","'user@user.com94'",3.93,"localtime()"),
		94 => array(20000094,"'User 95'","'Lastname 95'","'0'","'user@user.com95'",3.94,"localtime()"),
		95 => array(20000095,"'User 96'","'Lastname 96'","'0'","'user@user.com96'",3.95,"localtime()"),
		96 => array(20000096,"'User 97'","'Lastname 97'","'0'","'user@user.com97'",3.96,"localtime()"),
		97 => array(20000097,"'User 98'","'Lastname 98'","'0'","'user@user.com98'",3.97,"localtime()"),
		98 => array(20000098,"'User 99'","'Lastname 99'","'0'","'user@user.com99'",3.98,"localtime()"),
		99 => array(20000099,"'User 100'","'Lastname 100'","'0'","'user@user.com100'",3.99,"localtime()"),
	);

$enrollment_arr = array(
		0 => array(20000000,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		1 => array(20000001,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		2 => array(20000002,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		3 => array(20000003,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		4 => array(20000004,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		5 => array(20000005,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		6 => array(20000006,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		7 => array(20000007,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		8 => array(20000008,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		9 => array(20000009,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		10 => array(20000010,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		11 => array(20000011,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		12 => array(20000012,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		13 => array(20000013,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		14 => array(20000014,1000000,0,1,"localtime()","UNIX_TIMESTAMP()"),
		15 => array(20000015,1000000,0,2,"localtime()","UNIX_TIMESTAMP()"),
		16 => array(20000016,1000000,0,2,"localtime()","UNIX_TIMESTAMP()"),
		17 => array(20000017,1000000,0,2,"localtime()","UNIX_TIMESTAMP()"),
		18 => array(20000018,1000000,0,2,"localtime()","UNIX_TIMESTAMP()"),
		19 => array(20000019,1000000,0,2,"localtime()","UNIX_TIMESTAMP()"),
		20 => array(20000020,1000000,0,2,"localtime()+1","UNIX_TIMESTAMP()+1"),
		21 => array(20000021,1000000,0,3,"localtime()+2","UNIX_TIMESTAMP()+2"),
		22 => array(20000022,1000000,0,3,"localtime()+3","UNIX_TIMESTAMP()+3"),
		23 => array(20000023,1000000,0,3,"localtime()+4","UNIX_TIMESTAMP()+4"),
		24 => array(20000024,1000000,0,3,"localtime()+5","UNIX_TIMESTAMP()+5"),
		25 => array(20000025,1000000,0,3,"localtime()+6","UNIX_TIMESTAMP()+6"),
		26 => array(20000026,1000000,0,3,"localtime()+7","UNIX_TIMESTAMP()+7"),
		27 => array(20000027,1000000,0,3,"localtime()+8","UNIX_TIMESTAMP()+8"),
		28 => array(20000028,1000000,0,3,"localtime()+9","UNIX_TIMESTAMP()+9"),
		29 => array(20000029,1000000,0,3,"localtime()+10","UNIX_TIMESTAMP()+10"),
		30 => array(20000030,1000000,0,3,"localtime()+11","UNIX_TIMESTAMP()+11"),
		31 => array(20000031,1000000,0,3,"localtime()+12","UNIX_TIMESTAMP()+12"),
		32 => array(20000032,1000000,0,3,"localtime()+13","UNIX_TIMESTAMP()+13"),
		33 => array(20000033,1000000,0,3,"localtime()+14","UNIX_TIMESTAMP()+14"),
		34 => array(20000034,1000000,0,3,"localtime()+15","UNIX_TIMESTAMP()+15"),
		35 => array(20000035,1000000,0,3,"localtime()+16","UNIX_TIMESTAMP()+16"),
		36 => array(20000036,1000000,0,3,"localtime()+17","UNIX_TIMESTAMP()+17"),
		37 => array(20000037,1000000,0,3,"localtime()+18","UNIX_TIMESTAMP()+18"),
		38 => array(20000038,1000000,0,3,"localtime()+19","UNIX_TIMESTAMP()+19"),
		39 => array(20000039,1000000,0,3,"localtime()+20","UNIX_TIMESTAMP()+20"),
		40 => array(20000040,1000000,0,3,"localtime()+21","UNIX_TIMESTAMP()+21"),
		41 => array(20000041,1000001,0,4,"localtime()+22","UNIX_TIMESTAMP()+22"),
		42 => array(20000042,1000001,0,4,"localtime()+23","UNIX_TIMESTAMP()+23"),
		43 => array(20000043,1000001,0,4,"localtime()+24","UNIX_TIMESTAMP()+24"),
		44 => array(20000044,1000001,0,4,"localtime()+25","UNIX_TIMESTAMP()+25"),
		45 => array(20000045,1000001,0,4,"localtime()+26","UNIX_TIMESTAMP()+26"),
		46 => array(20000046,1000001,0,4,"localtime()+27","UNIX_TIMESTAMP()+27"),
		47 => array(20000047,1000001,0,4,"localtime()+28","UNIX_TIMESTAMP()+28"),
		48 => array(20000048,1000001,0,4,"localtime()+29","UNIX_TIMESTAMP()+29"),
		49 => array(20000049,1000001,0,4,"localtime()+30","UNIX_TIMESTAMP()+30"),
		50 => array(20000050,1000001,0,4,"localtime()+31","UNIX_TIMESTAMP()+31"),
		51 => array(20000051,1000001,0,4,"localtime()+32","UNIX_TIMESTAMP()+32"),
		52 => array(20000052,1000001,0,4,"localtime()+33","UNIX_TIMESTAMP()+33"),
		53 => array(20000053,1000001,0,4,"localtime()+34","UNIX_TIMESTAMP()+34"),
		54 => array(20000054,1000001,0,4,"localtime()+35","UNIX_TIMESTAMP()+35"),
		55 => array(20000055,1000001,0,4,"localtime()+36","UNIX_TIMESTAMP()+36"),
		56 => array(20000056,1000001,0,4,"localtime()+37","UNIX_TIMESTAMP()+37"),
		57 => array(20000057,1000001,0,4,"localtime()+38","UNIX_TIMESTAMP()+38"),
		58 => array(20000058,1000001,0,4,"localtime()+39","UNIX_TIMESTAMP()+39"),
		59 => array(20000059,1000001,0,4,"localtime()+40","UNIX_TIMESTAMP()+40"),
		60 => array(20000060,1000001,0,4,"localtime()+41","UNIX_TIMESTAMP()+41"),
		61 => array(20000061,1000001,0,4,"localtime()+42","UNIX_TIMESTAMP()+42"),
		62 => array(20000062,1000001,0,4,"localtime()+43","UNIX_TIMESTAMP()+43"),
		63 => array(20000063,1000001,0,4,"localtime()+44","UNIX_TIMESTAMP()+44"),
		64 => array(20000064,1000001,0,4,"localtime()+45","UNIX_TIMESTAMP()+45"),
		65 => array(20000065,1000001,0,4,"localtime()+46","UNIX_TIMESTAMP()+46"),
		66 => array(20000066,1000001,0,4,"localtime()+47","UNIX_TIMESTAMP()+47"),
		67 => array(20000067,1000001,0,4,"localtime()+48","UNIX_TIMESTAMP()+48"),
		68 => array(20000068,1000001,0,4,"localtime()+49","UNIX_TIMESTAMP()+49"),
		69 => array(20000069,1000001,0,4,"localtime()+50","UNIX_TIMESTAMP()+50"),
		70 => array(20000070,1000001,0,4,"localtime()+51","UNIX_TIMESTAMP()+51"),
		71 => array(20000071,1000001,0,4,"localtime()+52","UNIX_TIMESTAMP()+52"),
		72 => array(20000072,1000001,0,4,"localtime()+53","UNIX_TIMESTAMP()+53"),
		73 => array(20000073,1000001,0,4,"localtime()+54","UNIX_TIMESTAMP()+54"),
		74 => array(20000074,1000001,0,4,"localtime()+55","UNIX_TIMESTAMP()+55"),
		75 => array(20000075,1000001,0,4,"localtime()+56","UNIX_TIMESTAMP()+56"),
		76 => array(20000076,1000001,0,4,"localtime()+57","UNIX_TIMESTAMP()+57"),
		77 => array(20000077,1000001,0,4,"localtime()+58","UNIX_TIMESTAMP()+58"),
		78 => array(20000078,1000001,0,4,"localtime()+59","UNIX_TIMESTAMP()+59"),
		79 => array(20000079,1000001,0,4,"localtime()+60","UNIX_TIMESTAMP()+60"),
		80 => array(20000080,1000001,0,4,"localtime()+61","UNIX_TIMESTAMP()+61"),
		81 => array(20000081,1000001,0,4,"localtime()+62","UNIX_TIMESTAMP()+62"),
		82 => array(20000082,1000001,0,4,"localtime()+63","UNIX_TIMESTAMP()+63"),
		83 => array(20000083,1000001,0,4,"localtime()+64","UNIX_TIMESTAMP()+64"),
		84 => array(20000084,1000001,0,4,"localtime()+65","UNIX_TIMESTAMP()+65"),
		85 => array(20000085,1000001,0,4,"localtime()+66","UNIX_TIMESTAMP()+66"),
		86 => array(20000086,1000001,0,4,"localtime()+67","UNIX_TIMESTAMP()+67"),
		87 => array(20000087,1000001,0,4,"localtime()+68","UNIX_TIMESTAMP()+68"),
		88 => array(20000088,1000001,0,4,"localtime()+69","UNIX_TIMESTAMP()+69"),
		89 => array(20000089,1000001,0,4,"localtime()+70","UNIX_TIMESTAMP()+70"),
		90 => array(20000090,1000001,0,4,"localtime()+71","UNIX_TIMESTAMP()+71"),
	);

$sql_data[0] = generateInsert("subject", "subject_id, subject_title, subject_description, modification_date", $subject_arr);
$sql_data[1] = generateInsert("student", "user_id, name, lastname, classof_id, email, GPA, modification_date", $student_arr);
$sql_data[2] = generateInsert("classof", "classof_description", $classof_arr);
$sql_data[3] = generateInsert("semester", "semester_description", $semester_arr);
$sql_data[4] = generateInsert("subject_semester_classof", "subject_id, semester_id, classof_id, maxstudent, description, modification_date", $subject_semester_classof_arr);
$sql_data[5] = generateInsert("enrollment", "user_id, subject_id, semester_id, rank, modification_date, timestamp", $enrollment_arr);


//Submit data
submitData($sql_droptable, "Step1: Drop table");
submitData($sql_createtable, "Step2: create table");
for ($i=0;$i<count($sql_data);$i++)
{
	submitData($sql_data[$i], "Step3: prepare data - $i");	
}


?>