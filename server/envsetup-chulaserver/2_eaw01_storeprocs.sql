/******* SET UP Store Proc *******/
USE [eaw01]
GO
-- ================================================
-- Template generated from Template Explorer using:
-- Create Procedure (New Menu).SQL
--
-- Use the Specify Values for Template Parameters 
-- command (Ctrl-Shift-M) to fill in the parameter 
-- values below.
--
-- This block of comments will not be included in
-- the definition of the procedure.
-- ================================================
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		Wasa Choksuwattanasakul
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE listSubjectByClassOfAndSemester 
	-- Add the parameters for the stored procedure here
	@classof_id int = NULL,
	@semester nchar(10) = NULL 
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

    -- Insert statements for procedure here
	IF OBJECT_ID('tempdb..#TMP_SUBJECTCLASSOF') IS NOT NULL DROP TABLE #TMP_SUBJECTCLASSOF

	CREATE TABLE #TMP_SUBJECTCLASSOF(
	subject_id nchar(10),
	name nvarchar(256), 
	selected bit,
	classof_id nchar(10),
	semester nchar(10),
	minstudent int,
	maxstudent int,
	credit float,
	dayofweek int,
	timeofday int,
	instructor nvarchar(256),
	isRequired bit,
	addeddate datetime,
	updatedate datetime
	)

	INSERT INTO #TMP_SUBJECTCLASSOF 
			SELECT b.subject_id, 
			b.name,
			CAST(CASE WHEN classof_id is not NULL THEN 1 ELSE 0 END AS bit) AS selected, 
			classof_id, 
			semester, 
			minstudent, 
			maxstudent, 
			CAST(CASE WHEN credit is not NULL THEN credit ELSE b.defaultpoint END AS float) AS credit, 
			dayofweek, 
			timeofday, 
			instructor, 
			isRequired, 
			a.addeddate, 
			a.updatedate 
				FROM SUBJECT_CLASSOF a 
				RIGHT JOIN SUBJECT b ON a.subject_id = b.subject_id
				where (classof_id = @classof_id and semester = @semester) OR (classof_id is NULL and semester is NULL)
			
	INSERT INTO #TMP_SUBJECTCLASSOF (subject_id, name, credit, selected) (
	SELECT subject_id, name, defaultpoint, 0 FROM [SUBJECT] WHERE subject_id not in 
	(select subject_id from #TMP_SUBJECTCLASSOF))
			
	select * from #TMP_SUBJECTCLASSOF
END
GO

CREATE PROCEDURE listAccountMember 
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

    -- Insert statements for procedure here
	IF OBJECT_ID('tempdb..#TMP_LISTNONADMIN') IS NOT NULL DROP TABLE #TMP_LISTNONADMIN

	CREATE TABLE #TMP_LISTNONADMIN(
	user_id nchar(10),
	name nvarchar(256), 
	lastname nvarchar(256),
	user_type bit,
	updatedate datetime
	)

	INSERT INTO #TMP_LISTNONADMIN 
	SELECT DISTINCT
			a.student_id,
			a.name,
			a.lastname,
			1,
			a.updatedate 
				FROM STUDENT a 
				WHERE a.student_id in (SELECT user_id FROM ADMIN_USERS)

	INSERT INTO #TMP_LISTNONADMIN 
	SELECT DISTINCT
			a.student_id,
			a.name,
			a.lastname,
			0,
			a.updatedate 
				FROM STUDENT a 
				WHERE a.student_id not in (SELECT user_id FROM ADMIN_USERS)
			
			
	select * from #TMP_LISTNONADMIN order by user_type DESC, user_id ASC
END
GO


CREATE PROCEDURE getStudentDetail 
	-- Add the parameters for the stored procedure here
	@student_id nchar(10) = NULL
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

    -- Insert statements for procedure here
	IF OBJECT_ID('tempdb..#TMP_STUDENTDETAIL') IS NOT NULL DROP TABLE #TMP_STUDENTDETAIL

	CREATE TABLE #TMP_STUDENTDETAIL(
	subject_id nchar(10),
	name nvarchar(256), 
	lastname nvarchar(512),
	classof_id int,
	semester nchar(10),
	semester_state int,
	)

	INSERT INTO #TMP_STUDENTDETAIL 
			SELECT 
			a.student_id,
			a.name,
			a.lastname,
			a.classof_id,
			b.semester,
			b.semester_state
			FROM STUDENT a
			INNER JOIN CLASSOF_SEMESTER b ON a.classof_id = b.classof_id
			WHERE a.student_id = @student_id AND semester_state <> 0			
			
	select * from #TMP_STUDENTDETAIL
END
GO


-- =======================================================
-- It contains 3 random algorithm
-- 1. enrollFirstComeFirstServe
-- 2. enrollGPA
-- 3. enrollRanking
-- Each algorithm contains 3 parts
-- 1. select students who got accepted and not get accepted
-- 2. update ranking
-- 3. put data into TMP_SELECTION

-- =======================================================

CREATE PROCEDURE enrollFirstComeFirstServe 
	-- Add the parameters for the stored procedure here
	@subject_id nchar(10) = NULL,
	@classof_id int = NULL,
	@semester nchar(10) = NULL
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	DECLARE @maxstudent int
	DECLARE @maxpriority int
	SET @maxstudent = (SELECT maxstudent FROM SUBJECT_CLASSOF WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester)
	SET @maxpriority = (SELECT max(priority) FROM STUDENT_ENROLLMENT WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester)

    -- Insert statements for procedure here
	IF OBJECT_ID('tempdb..#TMP_STUDENTACCEPTED') IS NOT NULL DROP TABLE #TMP_STUDENTACCEPTED
	IF OBJECT_ID('tempdb..#TMP_STUDENTNOTACCEPTED') IS NOT NULL DROP TABLE #TMP_STUDENTNOTACCEPTED

	CREATE TABLE #TMP_STUDENTACCEPTED(
	student_id nchar(10),
	subject_id nchar(10),
	classof_id int,
	semester nchar(10),
	status nvarchar(50),
	credit float,
	dayofweek int,
	timeofday int,
	isRequired bit,
	priority int,
	logical_priority int,
	type nvarchar(50),
	addeddate datetime
	)

	CREATE TABLE #TMP_STUDENTNOTACCEPTED(
	student_id nchar(10),
	subject_id nchar(10),
	classof_id int,
	semester nchar(10),
	status nvarchar(50),
	credit float,
	dayofweek int,
	timeofday int,
	isRequired bit,
	priority int,
	logical_priority int,
	type nvarchar(50),
	addeddate datetime
	)

	-- GET accepted
	INSERT INTO #TMP_STUDENTACCEPTED
			SELECT top (@maxstudent)
			a.student_id,
			@subject_id,
			@classof_id,
			@semester,
			'SELECTED',
			b.credit,
			b.dayofweek,
			b.timeofday,
			b.isRequired,
			a.priority,
			a.logical_priority,
			type = CAST
				(CASE 
					WHEN a.logical_priority > (
						SELECT min(logical_priority) 
						FROM  STUDENT_ENROLLMENT 
						WHERE student_id = a.student_id AND a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
						) THEN 'STANDBY'
					WHEN a.logical_priority = (
						SELECT min(logical_priority) 
						FROM  STUDENT_ENROLLMENT 
						WHERE student_id = a.student_id AND a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
						) THEN 'ACCEPTED'
				END 
			AS NVARCHAR(10)),
			a.addeddate
			FROM STUDENT_ENROLLMENT a
			INNER JOIN SUBJECT_CLASSOF b ON a.subject_id = b.subject_id AND a.classof_id = b.classof_id AND a.semester = b.semester
			WHERE a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
			ORDER BY a.addeddate ASC, a.logical_priority ASC

	-- NOT GET ACCEPTED
	INSERT INTO #TMP_STUDENTNOTACCEPTED
			SELECT
			a.student_id,
			@subject_id,
			@classof_id,
			@semester,
			'NOTSELECTED',
			b.credit,
			b.dayofweek,
			b.timeofday,
			b.isRequired,
			a.priority,
			a.logical_priority,
			'STANDBY',
			a.addeddate
			FROM STUDENT_ENROLLMENT a
			INNER JOIN SUBJECT_CLASSOF b ON a.subject_id = b.subject_id AND a.classof_id = b.classof_id AND a.semester = b.semester
			WHERE a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
			AND a.student_id NOT IN (SELECT DISTINCT student_id FROM #TMP_STUDENTACCEPTED)
			ORDER BY a.addeddate ASC, a.logical_priority ASC

	-- update logical priority who don't get acceoted 
	UPDATE STUDENT_ENROLLMENT SET logical_priority = priority + @maxpriority
	WHERE student_id in (
		SELECT student_id FROM #TMP_STUDENTNOTACCEPTED 
	) AND subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester 

	-- update logical priority who get acceoted and almost accepted (เพราะว่านี่คือวิชาที่ priority สูงสุดของคนคนนี้ ณ​เวลานี้แล้ว ดังนั้น priority number นี้ของคนคนนี้จะต้องถูกปรับให้สูงขึ้นเพื่อไม่ให้priority นี้มีผลต่อการ weight priority ของวิชาถัดๆไป)
	UPDATE STUDENT_ENROLLMENT SET logical_priority = priority + (@maxpriority * 3)
	WHERE student_id in (
		SELECT student_id FROM #TMP_STUDENTACCEPTED 
		WHERE type = 'ACCEPTED'
	) AND subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester

	-- PUT Data into TMP_SELECTION
	DELETE FROM TMP_SELECTION WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester

	INSERT INTO TMP_SELECTION
		SELECT * FROM #TMP_STUDENTACCEPTED
	INSERT INTO TMP_SELECTION
		SELECT * FROM #TMP_STUDENTNOTACCEPTED

	-- select * from #TMP_STUDENTACCEPTED
	-- select * from #TMP_STUDENTNOTACCEPTED		

END
GO


CREATE PROCEDURE enrollGPA
	-- Add the parameters for the stored procedure here
	@subject_id nchar(10) = NULL,
	@classof_id int = NULL,
	@semester nchar(10) = NULL
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	DECLARE @maxstudent int
	DECLARE @maxpriority int
	SET @maxstudent = (SELECT maxstudent FROM SUBJECT_CLASSOF WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester)
	SET @maxpriority = (SELECT max(priority) FROM STUDENT_ENROLLMENT WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester)

    -- Insert statements for procedure here
	IF OBJECT_ID('tempdb..#TMP_STUDENTACCEPTED') IS NOT NULL DROP TABLE #TMP_STUDENTACCEPTED
	IF OBJECT_ID('tempdb..#TMP_STUDENTNOTACCEPTED') IS NOT NULL DROP TABLE #TMP_STUDENTNOTACCEPTED

	CREATE TABLE #TMP_STUDENTACCEPTED(
	student_id nchar(10),
	subject_id nchar(10),
	classof_id int,
	semester nchar(10),
	status nvarchar(50),
	credit float,
	dayofweek int,
	timeofday int,
	isRequired bit,
	priority int,
	logical_priority int,
	type nvarchar(50),
	addeddate datetime
	)

	CREATE TABLE #TMP_STUDENTNOTACCEPTED(
	student_id nchar(10),
	subject_id nchar(10),
	classof_id int,
	semester nchar(10),
	status nvarchar(50),
	credit float,
	dayofweek int,
	timeofday int,
	isRequired bit,
	priority int,
	logical_priority int,
	type nvarchar(50),
	addeddate datetime
	)

	-- GET accepted
	INSERT INTO #TMP_STUDENTACCEPTED
			SELECT top (@maxstudent)
			a.student_id,
			@subject_id,
			@classof_id,
			@semester,
			'SELECTED',
			b.credit,
			b.dayofweek,
			b.timeofday,
			b.isRequired,
			a.priority,
			a.logical_priority,
			type = CAST
				(CASE 
					WHEN a.logical_priority > (
						SELECT min(logical_priority) 
						FROM  STUDENT_ENROLLMENT 
						WHERE student_id = a.student_id AND a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
						) THEN 'STANDBY'
					WHEN a.logical_priority = (
						SELECT min(logical_priority) 
						FROM  STUDENT_ENROLLMENT 
						WHERE student_id = a.student_id AND a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
						) THEN 'ACCEPTED'
				END 
			AS NVARCHAR(10)),
			a.addeddate
			FROM STUDENT_ENROLLMENT a
			INNER JOIN SUBJECT_CLASSOF b ON a.subject_id = b.subject_id AND a.classof_id = b.classof_id AND a.semester = b.semester
			INNER JOIN STUDENT c ON a.student_id = c.student_id
			WHERE a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
			ORDER BY c.GPA DESC, a.addeddate ASC, a.logical_priority ASC

	-- NOT GET ACCEPTED
	INSERT INTO #TMP_STUDENTNOTACCEPTED
			SELECT
			a.student_id,
			@subject_id,
			@classof_id,
			@semester,
			'NOTSELECTED',
			b.credit,
			b.dayofweek,
			b.timeofday,
			b.isRequired,
			a.priority,
			a.logical_priority,
			'STANDBY',
			a.addeddate
			FROM STUDENT_ENROLLMENT a
			INNER JOIN SUBJECT_CLASSOF b ON a.subject_id = b.subject_id AND a.classof_id = b.classof_id AND a.semester = b.semester
			INNER JOIN STUDENT c ON a.student_id = c.student_id
			WHERE a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
			AND a.student_id NOT IN (SELECT DISTINCT student_id FROM #TMP_STUDENTACCEPTED)
			ORDER BY c.GPA DESC, a.addeddate ASC, a.logical_priority ASC

	-- update logical priority who don't get acceoted 
	UPDATE STUDENT_ENROLLMENT SET logical_priority = priority + @maxpriority
	WHERE student_id in (
		SELECT student_id FROM #TMP_STUDENTNOTACCEPTED 
	) AND subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester 

	-- update logical priority who get acceoted and almost accepted (เพราะว่านี่คือวิชาที่ priority สูงสุดของคนคนนี้ ณ​เวลานี้แล้ว ดังนั้น priority number นี้ของคนคนนี้จะต้องถูกปรับให้สูงขึ้นเพื่อไม่ให้priority นี้มีผลต่อการ weight priority ของวิชาถัดๆไป)
	UPDATE STUDENT_ENROLLMENT SET logical_priority = priority + (@maxpriority * 3)
	WHERE student_id in (
		SELECT student_id FROM #TMP_STUDENTACCEPTED 
		WHERE type = 'ACCEPTED'
	) AND subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester

	-- PUT Data into TMP_SELECTION
	DELETE FROM TMP_SELECTION WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester
	INSERT INTO TMP_SELECTION
		SELECT * FROM #TMP_STUDENTACCEPTED
	INSERT INTO TMP_SELECTION
		SELECT * FROM #TMP_STUDENTNOTACCEPTED

	-- select * from #TMP_STUDENTACCEPTED
	-- select * from #TMP_STUDENTNOTACCEPTED		

END
GO


CREATE PROCEDURE enrollRanking
	-- Add the parameters for the stored procedure here
	@subject_id nchar(10) = NULL,
	@classof_id int = NULL,
	@semester nchar(10) = NULL
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	DECLARE @maxstudent int
	DECLARE @maxpriority int
	SET @maxstudent = (SELECT maxstudent FROM SUBJECT_CLASSOF WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester)
	SET @maxpriority = (SELECT max(priority) FROM STUDENT_ENROLLMENT WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester)

    -- Insert statements for procedure here
	IF OBJECT_ID('tempdb..#TMP_STUDENTACCEPTED') IS NOT NULL DROP TABLE #TMP_STUDENTACCEPTED
	IF OBJECT_ID('tempdb..#TMP_STUDENTNOTACCEPTED') IS NOT NULL DROP TABLE #TMP_STUDENTNOTACCEPTED

	CREATE TABLE #TMP_STUDENTACCEPTED(
	student_id nchar(10),
	subject_id nchar(10),
	classof_id int,
	semester nchar(10),
	status nvarchar(50),
	credit float,
	dayofweek int,
	timeofday int,
	isRequired bit,
	priority int,
	logical_priority int,
	type nvarchar(50),
	addeddate datetime
	)

	CREATE TABLE #TMP_STUDENTNOTACCEPTED(
	student_id nchar(10),
	subject_id nchar(10),
	classof_id int,
	semester nchar(10),
	status nvarchar(50),
	credit float,
	dayofweek int,
	timeofday int,
	isRequired bit,
	priority int,
	logical_priority int,
	type nvarchar(50),
	addeddate datetime
	)

	-- GET accepted
	INSERT INTO #TMP_STUDENTACCEPTED
			SELECT top (@maxstudent)
			a.student_id,
			@subject_id,
			@classof_id,
			@semester,
			'SELECTED',
			b.credit,
			b.dayofweek,
			b.timeofday,
			b.isRequired,
			a.priority,
			a.logical_priority,
			type = CAST
				(CASE 
					WHEN a.logical_priority > (
						SELECT min(logical_priority) 
						FROM  STUDENT_ENROLLMENT 
						WHERE student_id = a.student_id AND a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
						) THEN 'STANDBY'
					WHEN a.logical_priority = (
						SELECT min(logical_priority) 
						FROM  STUDENT_ENROLLMENT 
						WHERE student_id = a.student_id AND a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
						) THEN 'ACCEPTED'
				END 
			AS NVARCHAR(10)),
			a.addeddate
			FROM STUDENT_ENROLLMENT a
			INNER JOIN SUBJECT_CLASSOF b ON a.subject_id = b.subject_id AND a.classof_id = b.classof_id AND a.semester = b.semester
			WHERE a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
			ORDER BY a.logical_priority ASC, a.addeddate ASC

	-- NOT GET ACCEPTED
	INSERT INTO #TMP_STUDENTNOTACCEPTED
			SELECT
			a.student_id,
			@subject_id,
			@classof_id,
			@semester,
			'NOTSELECTED',
			b.credit,
			b.dayofweek,
			b.timeofday,
			b.isRequired,
			a.priority,
			a.logical_priority,
			'STANDBY',
			a.addeddate
			FROM STUDENT_ENROLLMENT a
			INNER JOIN SUBJECT_CLASSOF b ON a.subject_id = b.subject_id AND a.classof_id = b.classof_id AND a.semester = b.semester
			WHERE a.subject_id = @subject_id AND a.classof_id = @classof_id AND a.semester = @semester
			AND a.student_id NOT IN (SELECT DISTINCT student_id FROM #TMP_STUDENTACCEPTED)
			ORDER BY a.logical_priority ASC, a.addeddate ASC

	-- update logical priority who don't get acceoted 
	UPDATE STUDENT_ENROLLMENT SET logical_priority = priority + @maxpriority
	WHERE student_id in (
		SELECT student_id FROM #TMP_STUDENTNOTACCEPTED 
	) AND subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester 

	-- update logical priority who get acceoted and almost accepted (เพราะว่านี่คือวิชาที่ priority สูงสุดของคนคนนี้ ณ​เวลานี้แล้ว ดังนั้น priority number นี้ของคนคนนี้จะต้องถูกปรับให้สูงขึ้นเพื่อไม่ให้priority นี้มีผลต่อการ weight priority ของวิชาถัดๆไป)
	UPDATE STUDENT_ENROLLMENT SET logical_priority = priority + (@maxpriority * 3)
	WHERE student_id in (
		SELECT student_id FROM #TMP_STUDENTACCEPTED
		WHERE type = 'ACCEPTED'
	) AND subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester

	-- PUT Data into TMP_SELECTION
	DELETE FROM TMP_SELECTION WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester
	INSERT INTO TMP_SELECTION
		SELECT * FROM #TMP_STUDENTACCEPTED
	INSERT INTO TMP_SELECTION
		SELECT * FROM #TMP_STUDENTNOTACCEPTED

	-- select * from #TMP_STUDENTACCEPTED
	-- select * from #TMP_STUDENTNOTACCEPTED		

END
GO

CREATE PROCEDURE enrollReconcile
	-- Add the parameters for the stored procedure here
	@subject_id nchar(10) = NULL,
	@classof_id int = NULL,
	@semester nchar(10) = NULL
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	DECLARE @maxstudent int
	SET @maxstudent = (SELECT maxstudent FROM SUBJECT_CLASSOF WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester)

    -- UPDATE STANDBY TO ALMOST ACCEPTED
    UPDATE TMP_SELECTION SET type = 'ACCEPTED'
    WHERE tmp_id IN (SELECT top (@maxstudent) tmp_id FROM TMP_SELECTION WHERE subject_id = @subject_id AND classof_id = @classof_id AND semester = @semester ORDER BY tmp_id ASC)

END
GO


CREATE procedure addEnrollmentSchedule
	@job nvarchar(128),
	@jobScheduleName nvarchar(128),
	@command1 nvarchar(max),
	@command2 nvarchar(max),
	@command3 nvarchar(max),
	@command4 nvarchar(max),
	@command5 nvarchar(max),
	@command6 nvarchar(max),
	@servername nvarchar(28),
	@startdate nvarchar(8),
	@starttime nvarchar(8)
AS
BEGIN
--Add a job
EXEC msdb.dbo.sp_add_job
    @job_name = @job ;
--Add a job step named process step. This step runs the stored procedure
EXEC msdb.dbo.sp_add_jobstep
    @job_name = @job,
    @step_name = N'process step 1',
    @step_id=1,
    @on_success_action=3,
    @subsystem = N'TSQL',
    @command = @command1

EXEC msdb.dbo.sp_add_jobstep
    @job_name = @job,
    @step_name = N'process step 2',
    @step_id=2,
    @on_success_action=3,
    @subsystem = N'TSQL',
    @command = @command2

EXEC msdb.dbo.sp_add_jobstep
    @job_name = @job,
    @step_name = N'process step 3',
    @step_id=3,
    @on_success_action=3,
    @subsystem = N'TSQL',
    @command = @command3

EXEC msdb.dbo.sp_add_jobstep
    @job_name = @job,
    @step_name = N'process step 4',
    @step_id=4,
    @on_success_action=3,
    @subsystem = N'TSQL',
    @command = @command4

EXEC msdb.dbo.sp_add_jobstep
    @job_name = @job,
    @step_name = N'process step 5',
    @step_id=5,
    @on_success_action=3,
    @subsystem = N'TSQL',
    @command = @command5

EXEC msdb.dbo.sp_add_jobstep
    @job_name = @job,
    @step_name = N'process step 6',
    @step_id=6,
    @on_success_action=1,
    @subsystem = N'TSQL',
    @command = @command6
--Schedule the job at a specified date and time
exec msdb.dbo.sp_add_jobschedule @job_name = @job,
@name = @jobScheduleName,
@freq_type=1,
@active_start_date = @startdate,
@active_start_time = @starttime
-- Add the job to the SQL Server Server
EXEC msdb.dbo.sp_add_jobserver
    @job_name =  @job

END
GO

CREATE procedure deactivateEnrollmentSchedule
	@job nvarchar(128),
	@jobScheduleName nvarchar(128),
	@command1 nvarchar(max),
	@command2 nvarchar(max),
	@servername nvarchar(28),
	@startdate nvarchar(8),
	@starttime nvarchar(8)
AS
BEGIN
--Add a job
EXEC msdb.dbo.sp_add_job
    @job_name = @job ;
--Add a job step named process step. This step runs the stored procedure
EXEC msdb.dbo.sp_add_jobstep
    @job_name = @job,
    @step_name = N'process step 1',
    @step_id=1,
    @on_success_action=3,
    @subsystem = N'TSQL',
    @command = @command1

EXEC msdb.dbo.sp_add_jobstep
    @job_name = @job,
    @step_name = N'process step 2',
    @step_id=2,
    @on_success_action=1,
    @subsystem = N'TSQL',
    @command = @command2

--Schedule the job at a specified date and time
exec msdb.dbo.sp_add_jobschedule @job_name = @job,
@name = @jobScheduleName,
@freq_type=1,
@active_start_date = @startdate,
@active_start_time = @starttime
-- Add the job to the SQL Server Server
EXEC msdb.dbo.sp_add_jobserver
    @job_name =  @job

END
GO