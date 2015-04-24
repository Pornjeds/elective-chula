<?php
/**
* 
*/
class AdminActivateSchedule
{
	var $app;
	var $db;
	var $classof_id;
	var $semester;
	var $activateScheduleName;
	var $deActivateScheduleName;
	var $activateJobName;
	var $deActivateJobName;

	function __construct($db, $classof_id, $semester)
	{
		$this->db = $db;
		$this->classof_id = $classof_id;
		$this->semester = $semester;
		$this->activateSchedule = $classof_id . '_' . $semester . '_activate_schedule';
		$this->deActivateSchedule = $classof_id . '_' . $semester . '_deactivate_schedule';
		$this->activateJob = $classof_id . '_' . $semester . '_activate_job';
		$this->deActivateJob = $classof_id . '_' . $semester . '_deactivate_job';
	}

	function addActivateSchedule($startDate, $startTime, $endDate, $endTime) {
		
		$updateSql = "UPDATE ADMIN_ACTIVATESCHEDULE set status = 1 where classof_id = $this->classof_id AND semester = $this->semester";
		$deleteSchedule_activate = "EXEC msdb.dbo.sp_delete_job @job_name = N'$this->activateSchedule' ;";
		$deleteJob_activate = "EXEC msdb.dbo.sp_delete_job @job_name = N'$this->activateJob' ;";
		$deleteSchedule_deactivate = "EXEC msdb.dbo.sp_delete_job @job_name = N'$this->deActivateSchedule' ;";
		$deleteJob_deactivate = "EXEC msdb.dbo.sp_delete_job @job_name = N'$this->deActivateJob' ;";
		
		if(!$this->db->setData($updateSql))
		{
			echo "fail 1: $updateSql<br>";
		}
		if(!$this->db->setData($deleteSchedule_activate))
		{
			echo "fail 2: $deleteSchedule_activate<br>";
		}
		if(!$this->db->setData($deleteJob_activate))
		{
			echo "fail 3: $deleteJob_activate<br>";
		}
		if(!$this->db->setData($deleteSchedule_deactivate))
		{
			echo "fail 4: $deleteSchedule_deactivate<br>";
		}
		if(!$this->db->setData($deleteJob_deactivate))
		{
			echo "fail 5: $deleteJob_deactivate<br>";
		}
		
		//Add job schedule - activate
		$command1 = "UPDATE CLASSOF_SEMESTER set semester_state = 0 where classof_id = $this->classof_id AND semester <> $this->semester and semester_state = 1";
		$command2 = "UPDATE CLASSOF_SEMESTER set semester_state = 1 where classof_id = $this->classof_id AND semester = $this->semester";
		$command3 = "DELETE FROM TMP_SELECTION where classof_id = $this->classof_id AND semester = $this->semester";
		$command4 = "UPDATE STUDENT_ENROLLMENT set logical_priority = priority where classof_id = $this->classof_id AND semester $this->semester";
		$command5 = "DELETE FROM STUDENT_CONFIRMED_ENROLLMENT where classof_id = $this->classof_id AND semester = $this->semester";

		$sql = "EXEC addEnrollmentSchedule 
					@job = '$this->activateJob', 
					@jobScheduleName = '$this->activateSchedule', 
					@command1 = N'$command1', 
					@command2 = N'$command2', 
					@command3 = N'$command3', 
					@command4 = N'$command4', 
					@command5 = N'$command5', 
					@startdate = '$startDate', 
					@startTime = '$startTime',
					@servername = 'localhost'
					";

		if(!$this->db->setData($sql))
		{
			echo "fail 6: $sql<br>";
		}

		//Add job schedule - deactivate
		$command1 = "UPDATE CLASSOF_SEMESTER set semester_state = 4 where classof_id = $this->classof_id AND semester = $this->semester";
		$sql = "EXEC deactivateEnrollmentSchedule 
					@job = '$this->deActivateJob', 
					@jobScheduleName = '$this->deActivateSchedule', 
					@command1 = N'$command1', 
					@startdate = '$endDate', 
					@startTime = '$endTime',
					@servername = 'localhost'
					";
		if(!$this->db->setData($sql))
		{
			echo "fail 7: $sql<br>";
		}
	}
}