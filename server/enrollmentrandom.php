<?

/*
3 functions:
	1. first come first serve
	2. sorting by GPA
	3. random out and move to the next rank
*/

include("DBManager.php");

function randomGen($min, $max, $quantity) {
    $numbers = range($min, $max);
    shuffle($numbers);
    return array_slice($numbers, 0, $quantity);
}

//1. First come first serve
function enrollment_firstcomefirstserve($subject_id, $semester_id, $classof_id)
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
			order by e.timestamp desc 
			limit $maxstudent";

	$result = $DBManager->getData($sql);
	$result_arr = array();
	$i = 0;
	while($row = mysqli_fetch_array($result))
	{
		$user_id = $row["user_id"];
		$GPA = $row["GPA"];
		$ranking = $row["rank"];
		$modification_date = $row["modification_date"];

		$result_arr[$i] = [$user_id, $GPA, $ranking, $modification_date];

		$i++;
	}

	return $result_arr;
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
	$result_arr = array();
	$i = 0;
	while($row = mysqli_fetch_array($result))
	{
		$user_id = $row["user_id"];
		$GPA = $row["GPA"];
		$ranking = $row["rank"];
		$modification_date = $row["modification_date"];

		$result_arr[$i] = [$user_id, $GPA, $ranking, $modification_date];

		$i++;
	}

	return $result_arr;
}




function randomByRanking($subject_id, $semester_id, $classof_id)
{
	$DBManager = new DBManager();

	//1. get maximum capacity
	$sql = "SELECT maxstudent FROM subject_semester_classof where subject_id = '$subject_id' and semester_id = '$semester_id' and classof_id = '$classof_id'";
	$result = $DBManager->getData($sql);
	$maxstudent = mysqli_fetch_array($result)["maxstudent"];
	echo "maxstudent: $maxstudent<br>";

	//2. get range of ranking under maxstudent
	//2.1 Get the first rank
	$sql = "SELECT rank FROM enrollment 
			where subject_id = '$subject_id' 
			and semester_id = '$semester_id' 
			order by rank asc
			limit 1";

	$result = $DBManager->getData($sql);
	$firstrank = mysqli_fetch_array($result)["rank"];
	echo "first rank $firstrank<br>";

	//2.2 Get the last rank
	$sql_lastrank_limit = $maxstudent-1;
	$sql = "SELECT rank FROM enrollment 
			where subject_id = '$subject_id' 
			and semester_id = '$semester_id' 
			order by rank asc
			limit $sql_lastrank_limit,1";
	$result = $DBManager->getData($sql);
	$lastrank = mysqli_fetch_array($result)["rank"];
	echo "the last rank $lastrank<br>";

	$result_arr = array();
	//3. Check if the firstrank and lastrank is the same
	if ($firstrank == $lastrank)
	{
		//Possibly need to random assign to student who assigned this rank to this subject 
		//For example, Subject A has limit 40 students. All student within this 40 assigned rank 1. So there was a posibility that the 41st student may assigned rank 1 as well)
		$sql = "SELECT * FROM enrollment e
			inner join student s ON e.user_id = s.user_id
			where e.subject_id = '$subject_id' 
			and e.semester_id = '$semester_id' 
			and e.rank = '$firstrank'";

		$result = $DBManager->getData($sql);
		$counttotal = $DBManager->countRow($result);
		$randomnums = randomGen(0, $counttotal-1, $maxstudent);
		$i = 0;
		while($row = mysqli_fetch_array($result))
		{
			$user_id = $row["user_id"];
			$GPA = $row["GPA"];
			$ranking = $row["rank"];
			$modification_date = $row["modification_date"];
			if(in_array($i,$randomnums))
			{
				$result_arr[$i] = [$user_id, $GPA, $ranking, $modification_date];
			}

			$i++;
		}
	}
	else
	{
		//This means there were more than a ranking assigned to this subject.
		//For example, Subject A has kimit 40 students. 25 students assigned rank 1, 10 student assigned rank 2 and 50 student assigned rank 3. Students who assigned rank 1 and 2 should already be accepted (25 + 10 = 35 which was still in 40). However, the 50 students assigned rank 3 should be randomly assiged (we selected only 5 from 50)

		//3.1 Count the total student who assign higher rank in this subject
		$sql1 = "SELECT * FROM enrollment e
			inner join student s ON e.user_id = s.user_id
			where e.subject_id = '$subject_id' 
			and e.semester_id = '$semester_id' 
			and e.rank < $lastrank";
		$result1 = $DBManager->getData($sql1);
		$count_enroll_higher_than_lowest_rank = $DBManager->countRow($result1);

		//these student should already be allowed because they assigned higher priority in this subject
		$i = 0;
		while($row1 = mysqli_fetch_array($result1))
		{
			$user_id = $row1["user_id"];
			$GPA = $row1["GPA"];
			$ranking = $row1["rank"];
			$modification_date = $row1["modification_date"];

			$result_arr[$i] = [$user_id, $GPA, $ranking, $modification_date];

			$i++;
		}

		//get all student who use this rank and randomly assign only this rank
		$sql2 = "SELECT * FROM enrollment e
			inner join student s ON e.user_id = s.user_id
			where e.subject_id = '$subject_id' 
			and e.semester_id = '$semester_id' 
			and e.rank = '$lastrank'";
		$result2 = $DBManager->getData($sql2);
		$count_enroll_lowest_rank = $DBManager->countRow($result2);
		echo "rank3 = $count_enroll_lowest_rank<br>";
		//need to random
		$randomnums = randomGen($i, $i+$count_enroll_lowest_rank-1, $maxstudent-$count_enroll_higher_than_lowest_rank);
		while($row2 = mysqli_fetch_array($result2))
		{
			$user_id = $row2["user_id"];
			$GPA = $row2["GPA"];
			$ranking = $row2["rank"];
			$modification_date = $row2["modification_date"];

			if(in_array($i,$randomnums))
			{
				$result_arr[$i] = [$user_id, $GPA, $ranking, $modification_date];
			}

			$i++;
		}
	}


	return $result_arr;
}


//$result_arr = enrollment_firstcomefirstserve(1000000, 0, 0);
//$result_arr = enrollment_gpa(1000000, 0, 0);
$result_arr = randomByRanking(1000000, 0, 0);

$len_result_arr = count($result_arr);
echo "Total: $len_result_arr";

if ($len_result_arr > 0)
	{
		$i=0;
		echo "<table border=1 cellpadding=3 cellspacing=0>
				<tr>
					<td>#</td>
					<td>user_id</td>
					<td>GPA</td>
					<td>Ranking</td>
					<td>Enrolled date</td>
				</tr>";
		foreach($result_arr as $result)
		{
			echo "<tr>
					<td>$i</td>
					<td>$result[0]</td>
					<td>$result[1]</td>
					<td>$result[2]</td>
					<td>$result[3]</td>
				</tr>";
			$i++;
		}
		echo "</table>";
}

?>