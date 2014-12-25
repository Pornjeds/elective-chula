<?

/*
3 functions:
	1. first come first serve
	2. sorting by GPA
	3. random out and move to the next rank
*/

include("DBManager.php");

//1. First come first serve
function enrollment_firstcomefirstserve($subject_id, $semester_id, $classof_id)
{
	$DBManager = new DBManager();

	//get maximum capacity
	$sql = "SELECT maxstudent FROM subject_semester_classof where subject_id = '$subject_id' and semester_id = '$semester_id' and classof_id = '$classof_id'";

	$result = $DBManager->getData($sql);
	$maxstudent = mysqli_fetch_array($result)["maxstudent"];

	$sql = "SELECT * FROM enrollment where subject_id = '$subject_id' and semester_id = '$semester_id' order by timestamp asc limit $maxstudent";
	$result = $DBManager->getData($sql);

	return $result;
}

function enrollment_gpa($subject_id, $semester_id, $classof_id)
{
	$DBManager = new DBManager();

	//get maximum capacity
	$sql = "SELECT maxstudent FROM subject_semester_classof where subject_id = '$subject_id' and semester_id = '$semester_id' and classof_id = '$classof_id'";

	$result = $DBManager->getData($sql);
	$maxstudent = mysqli_fetch_array($result)["maxstudent"];

	$sql = "SELECT * FROM enrollment e
			inner join student s ON e.user_id = s.user_id
			where e.subject_id = '$subject_id' 
			and e.semester_id = '$semester_id' 
			order by s.GPA desc 
			limit $maxstudent";

	$result = $DBManager->getData($sql);

	return $result;
}

//$result = enrollment_firstcomefirstserve(1000000, 0, 0);
$result = enrollment_gpa(1000000, 0, 0);
$i = 1;
while($row = mysqli_fetch_array($result))
{
	$user_id = $row["user_id"];
	$GPA = $row["GPA"];
	$modification_date = $row["modification_date"];

	echo "$i > $modification_date -> $user_id ($GPA)<br>";
	$i++;
}


?>