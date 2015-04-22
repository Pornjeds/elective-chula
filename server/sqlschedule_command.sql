
select * from ADMIN_ACTIVATESCHEDULE a
inner join CLASSOF_SEMESTER b ON a.classof_id = b.classof_id AND a.semester = b.semester
where a.status = 0

-- 2 is classof_id
-- 4 is semester

use msdb;
GO
EXEC dbo.sp_add_job
	@job_name = N'2_4_activate';
GO

use msdb;
GO
EXEC dbo.sp_add_job
	@job_name = N'2_4_deactivate';
GO

USE msdb;
GO
EXEC sp_add_jobstep
	@job_name = N'2_4_activate',
	@step_name = N'RemoveActivateJob',
	@subsystem = N'TSQL',
	@command = N'DELETE FROM ADMIN_ACTIVATESCHEDULE where classof_id = 2 AND semester = 4',
	@retry_attempts = 2,
	@retry_interval = 2 ;

EXEC sp_add_jobstep
	@job_name = N'2_4_activate',
	@step_name = N'sqlSemesterState',
	@subsystem = N'TSQL',
	@command = N'UPDATE CLASSOF_SEMESTER set semester_state = 0 where classof_id = 2 AND semester <> 4',
	@retry_attempts = 2,
	@retry_interval = 2 ;

EXEC sp_add_jobstep
	@job_name = N'2_4_activate',
	@step_name = N'sqlClearTmpSelection',
	@subsystem = N'TSQL',
	@command = N'DELETE FROM TMP_SELECTION where classof_id = 2 AND semester = 4',
	@retry_attempts = 2,
	@retry_interval = 2 ;
	
EXEC sp_add_jobstep
	@job_name = N'2_4_activate',
	@step_name = N'sqlSetLogicalPriority',
	@subsystem = N'TSQL',
	@command = N'UPDATE STUDENT_ENROLLMENT set logical_priority = priority where classof_id = 2 AND semester = 4',
	@retry_attempts = 2,
	@retry_interval = 2 ;

EXEC sp_add_jobstep
	@job_name = N'2_4_activate',
	@step_name = N'sqlClearStudentConfirmedEnrollment',
	@subsystem = N'TSQL',
	@command = N'DELETE FROM STUDENT_CONFIRMED_ENROLLMENT where classof_id = 2 AND semester = 4',
	@retry_attempts = 2,
	@retry_interval = 2 ;

GO

USE msdb ;
GO
EXEC dbo.sp_add_schedule
    @schedule_name = N'2_4_activate_RunOnce',
    @freq_type = 1,
    @active_start_date = 20150422,
    @active_start_time = 084900 ;
GO

EXEC sp_attach_schedule
   @job_name = N'2_4_activate',
   @schedule_name = N'2_4_activate_RunOnce' ;
GO


USE msdb ;
GO

EXEC dbo.sp_add_jobserver
    @job_name = N'2_4_activate'
GO