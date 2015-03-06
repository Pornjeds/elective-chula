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
-- Author:		<Author,,Name>
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
				WHERE a.student_id in (SELECT user_id FROM USER_ROLE)

	INSERT INTO #TMP_LISTNONADMIN 
	SELECT DISTINCT
			a.admin_id,
			a.name,
			a.lastname,
			1,
			a.updatedate 
				FROM ADMIN_MEMBER a 
				WHERE CAST(a.admin_id AS NCHAR(10)) in (SELECT user_id FROM USER_ROLE)

	INSERT INTO #TMP_LISTNONADMIN 
	SELECT DISTINCT
			a.student_id,
			a.name,
			a.lastname,
			0,
			a.updatedate 
				FROM STUDENT a 
				WHERE a.student_id not in (SELECT user_id FROM USER_ROLE)

	INSERT INTO #TMP_LISTNONADMIN 
	SELECT DISTINCT
			a.admin_id,
			a.name,
			a.lastname,
			0,
			a.updatedate 
				FROM ADMIN_MEMBER a 
				WHERE CAST(a.admin_id AS NCHAR(10)) not in (SELECT user_id FROM USER_ROLE)
			
			
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

