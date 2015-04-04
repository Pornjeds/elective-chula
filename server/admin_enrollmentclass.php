<?php
/**
* 
*/
class AdminStudentEnrollment
{
	var $app;
	var $db;
	var $classof_id;
	var $semester;
	var $mincredit;
	var $maxcredit;
	var $pickmethod_id;
	var $subject_id_sortedby_studentcount;
	function __construct($app, $db, $classof_id, $semester)
	{
		$app = $this->app;
		$this->db = $db;
		$this->classof_id = $classof_id;
		$this->semester = $semester;
		$this->subject_id_sortedby_studentcount = array();
	}

	function getSemesterState(){
		$sql = "SELECT semester_state, mincredit, maxcredit, pickmethod_id FROM CLASSOF_SEMESTER WHERE classof_id = '".$this->classof_id."' AND semester = '".$this->semester."'";
		$result = $this->db->getData($sql);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$semester_state = $row['semester_state'];
				$this->mincredit = $row['mincredit'];
				$this->maxcredit = $row['maxcredit'];
				$this->pickmethod_id = $row['pickmethod_id'];
			}
		}

		return $semester_state;
	}

	function getSubjectArraySortedStudentEnrollment(){
		$sql = "select 
				a.subject_id,
				COUNT(c.student_id) AS studentcount
				FROM SUBJECT_CLASSOF a
				LEFT JOIN STUDENT_ENROLLMENT c ON a.subject_id = c.subject_id
				WHERE a.classof_id = '".$this->classof_id."' AND a.semester = '".$this->semester."'
				GROUP BY a.subject_id
				ORDER BY studentcount DESC";

		$result = $this->db->getData($sql);
		if ($result){
			while($row = sqlsrv_fetch_array($result)){
				$subject_id = trim($row['subject_id']);
				array_push($this->subject_id_sortedby_studentcount, $subject_id);
			}
		}
		return $this->subject_id_sortedby_studentcount;
	}

	function enrollFirstComeFirstServe($subject_id){
		$sql = "exec enrollFirstComeFirstServe @subject_id = '".$subject_id."', @classof_id = '".$this->classof_id."', @semester = '".$this->semester."'";
		if(!$this->db->setData($sql))
		{
			$this->db->rollbackWork();
			$this->app->response->setBody(json_encode(array("status"=>"fail enrollment enrollFirstComeFirstServe")));
			return;
		}
	}

	function enrollGPA($subject_id){
		$sql = "exec enrollGPA @subject_id = '".$subject_id."', @classof_id = '".$this->classof_id."', @semester = '".$this->semester."'";
		if(!$this->db->setData($sql))
		{
			$this->db->rollbackWork();
			$this->app->response->setBody(json_encode(array("status"=>"fail enrollment enrollGPA")));
			return;
		}
	}

	function enrollRanking($subject_id){

		$sql = "exec enrollRanking @subject_id = '".$subject_id."', @classof_id = '".$this->classof_id."', @semester = '".$this->semester."'";
		if(!$this->db->setData($sql))
		{
			$this->db->rollbackWork();
			$this->app->response->setBody(json_encode(array("status"=>"fail enrollment enrollRanking")));
			return;
		}

	}

	function enrollReconcile($subject_id){
		$sql = "exec enrollReconcile @subject_id = '".$subject_id."', @classof_id = '".$this->classof_id."', @semester = '".$this->semester."'";
		if(!$this->db->setData($sql))
		{
			$this->db->rollbackWork();
			$this->app->response->setBody(json_encode(array("status"=>"fail enrollReconcile")));
			return;
		}
	}

	function listStudentFromTmpSelectionSortedByAcceptedCount(){
		$student_arr = array();
		$sql = "SELECT student_id, 
				SUM(CASE WHEN type='ACCEPTED' THEN credit ELSE 0 END) AS accepted_credit
				FROM TMP_SELECTION
				WHERE classof_id = '".$this->classof_id."' AND semester = '".$this->semester."'
				GROUP BY student_id
				ORDER BY accepted_credit DESC, student_id ASC";
		$result = $this->db->getData($sql);
		if($result){
			while($row = sqlsrv_fetch_array($result)){
				//ดึง credit ของแต่ละวิชาที่นศคนนี้ได้มาคำนวณเพื่อหาว่าเค้าควรจะได้เรียนกี่วิชา 
				array_push($student_arr, $row['student_id']);	
			}
		}

		return $student_arr;
	}

	function getStudentSubjectConfirmedList($student_id){
		$current_sum_credit = 0;
		$subject_id_confirmed_list = "";
		$subject_id_tobe_removed_arr = array();
		$dayofweek_timeofday = array();
		$duplicate_date_and_time = false;
		$thisuser_dont_have_enough_credit = false;
		
		$sql = "SELECT subject_id, credit, dayofweek, timeofday, type
				FROM TMP_SELECTION 
				WHERE student_id = '".$student_id."' AND type = 'ACCEPTED' AND classof_id = '".$this->classof_id."' AND semester = '".$this->semester."'
				ORDER BY priority ASC";
		
		$result = $this->db->getData($sql);
		if($result){
			
			while($row = sqlsrv_fetch_array($result)){

				$duplicate_date_and_time = false;
				$subject_id = trim($row['subject_id']);
				$subject_credit = trim($row['credit']);
				$dayofweek = trim($row['dayofweek']);
				$timeofday = trim($row['timeofday']);
				$type = trim($row['type']);
				//check ก่อนว่ามันไปทับกับ วันและเวลาที่เราลงทะเบียนได้แล้วรึป่าว - ซึ่งลำดับมันจะเรียงตาม priority อยู่แล้ว 
				foreach($dayofweek_timeofday as $dt){
					if ($dayofweek == $dt["dayofweek"] && $timeofday == $dt["timeofday"]){
						$duplicate_date_and_time = true;
					}
				}

				//handle กรณีที่วิชาเดียวเปิดหลายวัน (เช่น SM เปิด 2 sec)
				//subject_id จะเป็น 1000012-1 กับ 1000012-2
				//ในกรณีนี้ ถ้าได้วิชาไหนก่อนก็ตัดอีกวิชาทิ้งไป
				$need_to_be_added_to_confirmed_list = true;
				$subject_id_exploded = explode('-', $subject_id);
				if (count($subject_id_exploded) > 1) {
					$subject_id_withoutSec = $subject_id_exploded[0];
					if (strpos($subject_id_confirmed_list, $subject_id_withoutSec) != false) {
						$need_to_be_added_to_confirmed_list = false;
					} else {
						$need_to_be_added_to_confirmed_list = true;
					}
				}
				
				if ($current_sum_credit + $subject_credit <= $this->maxcredit && !$duplicate_date_and_time && $need_to_be_added_to_confirmed_list){
					$current_sum_credit += $subject_credit;

					$subject_id_confirmed_list .= "'$subject_id',";
					array_push($dayofweek_timeofday, array("dayofweek" => $dayofweek, "timeofday" => $timeofday ));
				} else {
					array_push($subject_id_tobe_removed_arr, $subject_id);
				}

			}

			$subject_id_confirmed_list = substr($subject_id_confirmed_list, 0, -1);

			if ($current_sum_credit < $this->maxcredit){
				$thisuser_dont_have_enough_credit = true;
			}
	
		}

		return array(
			"subject_id_confirmed_list" => $subject_id_confirmed_list,
			"subject_id_tobe_removed_arr" => $subject_id_tobe_removed_arr,
			"thisuser_dont_have_enough_credit" => $thisuser_dont_have_enough_credit
		);
	}

	function markAcceptedHighPrioritySubjectStatusToConfirmed($student_id, $subject_id_confirmed_list){
		$sql = "UPDATE TMP_SELECTION SET status = 'CONFIRMED', type = 'ACCEPTED' WHERE student_id = '".$student_id."' AND classof_id = '".$this->classof_id."' AND semester = '".$this->semester."' 
		AND subject_id in (".$subject_id_confirmed_list.")";

		if(!$this->db->setData($sql))
		{
			$this->db->rollbackWork();
			$this->app->response->setBody(json_encode(array("status"=>"fail markAcceptedHighPrioritySubjectStatusToConfirmed")));
			return;
		}
	}

	function removeAcceptedLowPrioritySubjectStatus($student_id, $subject_id){
		$sql = "DELETE FROM TMP_SELECTION WHERE subject_id = '".$subject_id."' AND type = 'ACCEPTED' AND classof_id = '".$this->classof_id."' AND semester = '".$this->semester."' AND student_id = '".$student_id."'";
		if(!$this->db->setData($sql))
		{
			$this->db->rollbackWork();
			$this->app->response->setBody(json_encode(array("status"=>"fail removeAcceptedLowPrioritySubjectStatus")));
			return;
		}
	}

	function moveAllConfrimedAcceptedStudentsFromTmpSelectionToStudentConfirmedEnrollment($classof_id, $semester){
		$sql = "INSERT INTO STUDENT_CONFIRMED_ENROLLMENT
				SELECT student_id, subject_id, classof_id, semester, addeddate FROM TMP_SELECTION
				WHERE classof_id = '$classof_id' AND semester = '$semester' AND status = 'CONFIRMED' AND type = 'ACCEPTED'";
		if(!$this->db->setData($sql))
		{
			$this->db->rollbackWork();
			$this->app->response->setBody(json_encode(array("status"=>"fail removeAcceptedLowPrioritySubjectStatus")));
			return;
		}
	}

	function setStatusClassOfSemester($semester_state){
		$sql = "UPDATE CLASSOF_SEMESTER SET semester_state = '".$semester_state."' WHERE classof_id = '".$this->classof_id."' AND semester = '".$this->semester."'";
		if(!$this->db->setData($sql))
		{
			$this->db->rollbackWork();
			$this->app->response->setBody(json_encode(array("status"=>"fail setStatusClassOfSemester")));
			return;
		}
	}
}
?>