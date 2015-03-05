USE [master]
GO

/****** Object:  Database [eaw01]    Script Date: 01/16/2015 08:06:56 ******/
ALTER DATABASE eaw01
SET OFFLINE WITH ROLLBACK IMMEDIATE;

DROP DATABASE [eaw01];
GO

USE [master]
GO

/****** Object:  Database [eaw01]    Script Date: 01/16/2015 08:06:56 ******/
CREATE DATABASE [eaw01] ON  PRIMARY 
( NAME = N'eaw01', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL10_50.MSSQLSERVER\MSSQL\DATA\eaw01.mdf' , SIZE = 2048KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'eaw01_log', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL10_50.MSSQLSERVER\MSSQL\DATA\eaw01_log.ldf' , SIZE = 1024KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO

ALTER DATABASE [eaw01] SET COMPATIBILITY_LEVEL = 100
GO

IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [eaw01].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO

ALTER DATABASE [eaw01] SET ANSI_NULL_DEFAULT OFF 
GO

ALTER DATABASE [eaw01] SET ANSI_NULLS OFF 
GO

ALTER DATABASE [eaw01] SET ANSI_PADDING OFF 
GO

ALTER DATABASE [eaw01] SET ANSI_WARNINGS OFF 
GO

ALTER DATABASE [eaw01] SET ARITHABORT OFF 
GO

ALTER DATABASE [eaw01] SET AUTO_CLOSE OFF 
GO

ALTER DATABASE [eaw01] SET AUTO_CREATE_STATISTICS ON 
GO

ALTER DATABASE [eaw01] SET AUTO_SHRINK OFF 
GO

ALTER DATABASE [eaw01] SET AUTO_UPDATE_STATISTICS ON 
GO

ALTER DATABASE [eaw01] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO

ALTER DATABASE [eaw01] SET CURSOR_DEFAULT  GLOBAL 
GO

ALTER DATABASE [eaw01] SET CONCAT_NULL_YIELDS_NULL OFF 
GO

ALTER DATABASE [eaw01] SET NUMERIC_ROUNDABORT OFF 
GO

ALTER DATABASE [eaw01] SET QUOTED_IDENTIFIER OFF 
GO

ALTER DATABASE [eaw01] SET RECURSIVE_TRIGGERS OFF 
GO

ALTER DATABASE [eaw01] SET  DISABLE_BROKER 
GO

ALTER DATABASE [eaw01] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO

ALTER DATABASE [eaw01] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO

ALTER DATABASE [eaw01] SET TRUSTWORTHY OFF 
GO

ALTER DATABASE [eaw01] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO

ALTER DATABASE [eaw01] SET PARAMETERIZATION SIMPLE 
GO

ALTER DATABASE [eaw01] SET READ_COMMITTED_SNAPSHOT OFF 
GO

ALTER DATABASE [eaw01] SET HONOR_BROKER_PRIORITY OFF 
GO

ALTER DATABASE [eaw01] SET  READ_WRITE 
GO

ALTER DATABASE [eaw01] SET RECOVERY FULL 
GO

ALTER DATABASE [eaw01] SET  MULTI_USER 
GO

ALTER DATABASE [eaw01] SET PAGE_VERIFY CHECKSUM  
GO

ALTER DATABASE [eaw01] SET DB_CHAINING OFF 
GO

/* Database creation is done, next create table*/

USE [eaw01]
GO
/****** Object:  Table [dbo].[STUDENT]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[STUDENT](
	[student_id] [nchar](10) NOT NULL,
	[classof_id] [int] NOT NULL,
	[name] [nvarchar](256) NOT NULL,
	[lastname] [nvarchar](512) NOT NULL,
	[email] [nvarchar](256) NOT NULL,
	[password] [nvarchar](256) NOT NULL,
	[profilepic] [nvarchar](256) NOT NULL,
	[GPA] [float] NOT NULL,
	[addeddate] [datetime] NOT NULL,
	[updatedate] [datetime] NULL,
	[student_status] [tinyint] NOT NULL,
 CONSTRAINT [PK_STUDENT] PRIMARY KEY CLUSTERED 
(
	[student_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[CLASSOF]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[CLASSOF](
	[classof_id] [int] NOT NULL IDENTITY(1,1),
	[classof_description] [nchar](10) NOT NULL,
 CONSTRAINT [PK_CLASSOF] PRIMARY KEY CLUSTERED 
(
	[classof_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[ADMIN_MEMBER]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ADMIN_MEMBER](
	[admin_id] [int] NOT NULL IDENTITY(1,1),
	[name] [nvarchar](256) NULL,
	[lastname] [nvarchar](512) NULL,
	[email] [nvarchar](256) NULL,
	[password] [nvarchar](256) NULL,
	[addeddate] [datetime] NULL,
	[updatedate] [datetime] NULL,
 CONSTRAINT [PK_ADMIN_MEMBER] PRIMARY KEY CLUSTERED 
(
	[admin_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[SUBJECT]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[SUBJECT](
	[subject_id] [nchar](10) NOT NULL,
	[name] [nvarchar](256) NOT NULL,
	[description] [text] NOT NULL,
	[defaultpoint] [float] NOT NULL,
	[addeddate] [datetime] NOT NULL,
	[updatedate] [datetime] NULL,
 CONSTRAINT [PK_SUBJECT] PRIMARY KEY CLUSTERED 
(
	[subject_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[USER_ROLE]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[USER_ROLE](
	[user_id] [nchar](10) NOT NULL,
	[role] [nvarchar](50) NOT NULL,
	[updatedate] [datetime] NOT NULL,
 CONSTRAINT [PK_USER_ROLE] PRIMARY KEY CLUSTERED 
(
	[user_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[TMP_SELECTION]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[TMP_SELECTION](
	[student_id] [nchar](10) NOT NULL,
	[subject_id] [nchar](10) NOT NULL,
	[classof_id] [int] NOT NULL,
	[semester] [nchar](10) NOT NULL,
	[status] [nvarchar](50) NOT NULL,
	[type] [nvarchar](50) NOT NULL,
	[addeddate] [datetime] NOT NULL,
 CONSTRAINT [PK_TMP_SELECTION] PRIMARY KEY CLUSTERED 
(
	[student_id] ASC,
	[subject_id] ASC,
	[classof_id] ASC,
	[semester] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[SUBJECT_CLASSOF]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[SUBJECT_CLASSOF](
	[subject_id] [nchar](10) NOT NULL,
	[classof_id] [int] NOT NULL,
	[semester] [nchar](10) NOT NULL,
	[minstudent] [int] NOT NULL,
	[maxstudent] [int] NOT NULL,
	[credit] [float] NOT NULL,
	[dayofweek] [int] NOT NULL,
	[timeofday] [int] NOT NULL,
	[instructor] [nvarchar](256) NOT NULL,
	[isRequired] [bit] NOT NULL,
	[addeddate] [datetime] NOT NULL,
	[updatedate] [datetime] NULL,
 CONSTRAINT [PK_SUBJECT_CLASSOF] PRIMARY KEY CLUSTERED 
(
	[subject_id] ASC,
	[classof_id] ASC,
	[semester] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[SEMESTER_CLASSOF]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[CLASSOF_SEMESTER](
	[classof_id] [int] NOT NULL,
	[semester] [nchar](10) NOT NULL,
	[mincredit] [int] NOT NULL,
	[maxcredit] [int] NOT NULL,
	[pickmethod_id] [int] NOT NULL,
	[semester_state] [bit] NOT NULL,
	[addeddate] [datetime] NOT NULL,
	[updatedate] [datetime] NULL,
 CONSTRAINT [PK_CLASSOF_SEMESTER] PRIMARY KEY CLUSTERED 
(
	[classof_id] ASC,
	[semester] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[PICKMETHOD]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[PICKMETHOD](
	[pickmethod_id] [int] NOT NULL IDENTITY(1,1),
	[name] [nvarchar](200) NOT NULL,
	[addeddate] [datetime] NOT NULL,
	[updatedate] [datetime] NULL,
 CONSTRAINT [PK_PICKMETHOD] PRIMARY KEY CLUSTERED 
(
	[pickmethod_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[STUDENT_ENROLLMENT]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[STUDENT_ENROLLMENT](
	[student_id] [nchar](10) NOT NULL,
	[subject_id] [nchar](10) NOT NULL,
	[classof_id] [int] NOT NULL,
	[semester] [nchar](10) NOT NULL,
	[priority] [int] NOT NULL,
	[addeddate] [datetime] NOT NULL,
 CONSTRAINT [PK_STUDENT_ENROLLMENT] PRIMARY KEY CLUSTERED 
(
	[student_id] ASC,
	[subject_id] ASC,
	[classof_id] ASC,
	[semester] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[STUDENT_CONFIRMED_ENROLLMENT]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[STUDENT_CONFIRMED_ENROLLMENT](
	[student_id] [nchar](10) NOT NULL,
	[subject_id] [nchar](10) NOT NULL,
	[classof_id] [int] NOT NULL,
	[semester_id] [nchar](10) NOT NULL,
	[addeddate] [datetime] NOT NULL,
 CONSTRAINT [PK_STUDENT_CONFIRMED_ENROLLMENT] PRIMARY KEY CLUSTERED 
(
	[student_id] ASC,
	[subject_id] ASC,
	[classof_id] ASC,
	[semester_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ADMIN_AUDITLOG]    Script Date: 01/12/2015 23:44:56 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ADMIN_AUDITLOG](
	[id] [int] NOT NULL IDENTITY(1,1),
	[user_id] [nchar](10) NOT NULL,
	[activity] [text] NOT NULL,
	[logdate] [datetime] NOT NULL,
 CONSTRAINT [PK_ADMIN_AUDITLOG] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

/****** Object:  ForeignKey [FK_ADMIN_AUDITLOG_USER_ROLE]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[ADMIN_AUDITLOG]  WITH CHECK ADD  CONSTRAINT [FK_ADMIN_AUDITLOG_USER_ROLE] FOREIGN KEY([user_id])
REFERENCES [dbo].[USER_ROLE] ([user_id])
GO
ALTER TABLE [dbo].[ADMIN_AUDITLOG] CHECK CONSTRAINT [FK_ADMIN_AUDITLOG_USER_ROLE]
GO
/****** Object:  ForeignKey [FK_STUDENT_CONFIRMED_ENROLLMENT_CLASSOF]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[STUDENT_CONFIRMED_ENROLLMENT]  WITH CHECK ADD  CONSTRAINT [FK_STUDENT_CONFIRMED_ENROLLMENT_CLASSOF] FOREIGN KEY([classof_id])
REFERENCES [dbo].[CLASSOF] ([classof_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[STUDENT_CONFIRMED_ENROLLMENT] CHECK CONSTRAINT [FK_STUDENT_CONFIRMED_ENROLLMENT_CLASSOF]
GO
/****** Object:  ForeignKey [FK_STUDENT_CONFIRMED_ENROLLMENT_STUDENT]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[STUDENT_CONFIRMED_ENROLLMENT]  WITH CHECK ADD  CONSTRAINT [FK_STUDENT_CONFIRMED_ENROLLMENT_STUDENT] FOREIGN KEY([student_id])
REFERENCES [dbo].[STUDENT] ([student_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[STUDENT_CONFIRMED_ENROLLMENT] CHECK CONSTRAINT [FK_STUDENT_CONFIRMED_ENROLLMENT_STUDENT]
GO
/****** Object:  ForeignKey [FK_STUDENT_CONFIRMED_ENROLLMENT_SUBJECT]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[STUDENT_CONFIRMED_ENROLLMENT]  WITH CHECK ADD  CONSTRAINT [FK_STUDENT_CONFIRMED_ENROLLMENT_SUBJECT] FOREIGN KEY([subject_id])
REFERENCES [dbo].[SUBJECT] ([subject_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[STUDENT_CONFIRMED_ENROLLMENT] CHECK CONSTRAINT [FK_STUDENT_CONFIRMED_ENROLLMENT_SUBJECT]
GO
/****** Object:  ForeignKey [FK_STUDENT_ENROLLMENT_CLASSOF]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[STUDENT_ENROLLMENT]  WITH CHECK ADD  CONSTRAINT [FK_STUDENT_ENROLLMENT_CLASSOF] FOREIGN KEY([classof_id])
REFERENCES [dbo].[CLASSOF] ([classof_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[STUDENT_ENROLLMENT] CHECK CONSTRAINT [FK_STUDENT_ENROLLMENT_CLASSOF]
GO
/****** Object:  ForeignKey [FK_STUDENT_ENROLLMENT_STUDENT]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[STUDENT_ENROLLMENT]  WITH CHECK ADD  CONSTRAINT [FK_STUDENT_ENROLLMENT_STUDENT] FOREIGN KEY([student_id])
REFERENCES [dbo].[STUDENT] ([student_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[STUDENT_ENROLLMENT] CHECK CONSTRAINT [FK_STUDENT_ENROLLMENT_STUDENT]
GO
/****** Object:  ForeignKey [FK_STUDENT_ENROLLMENT_SUBJECT]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[STUDENT_ENROLLMENT]  WITH CHECK ADD  CONSTRAINT [FK_STUDENT_ENROLLMENT_SUBJECT] FOREIGN KEY([subject_id])
REFERENCES [dbo].[SUBJECT] ([subject_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[STUDENT_ENROLLMENT] CHECK CONSTRAINT [FK_STUDENT_ENROLLMENT_SUBJECT]
GO
/****** Object:  ForeignKey [FK_SUBJECT_CLASSOF_CLASSOF]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[SUBJECT_CLASSOF]  WITH CHECK ADD  CONSTRAINT [FK_SUBJECT_CLASSOF_CLASSOF] FOREIGN KEY([classof_id])
REFERENCES [dbo].[CLASSOF] ([classof_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[SUBJECT_CLASSOF] CHECK CONSTRAINT [FK_SUBJECT_CLASSOF_CLASSOF]
GO
/****** Object:  ForeignKey [FK_SUBJECT_CLASSOF_SUBJECT]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[SUBJECT_CLASSOF]  WITH CHECK ADD  CONSTRAINT [FK_SUBJECT_CLASSOF_SUBJECT] FOREIGN KEY([subject_id])
REFERENCES [dbo].[SUBJECT] ([subject_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[SUBJECT_CLASSOF] CHECK CONSTRAINT [FK_SUBJECT_CLASSOF_SUBJECT]
GO
/****** Object:  ForeignKey [FK_SUBJECT_CLASSOF_CLASSOF]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[CLASSOF_SEMESTER]  WITH CHECK ADD  CONSTRAINT [FK_CLASSOF_SEMESTER_CLASSOF] FOREIGN KEY([classof_id])
REFERENCES [dbo].[CLASSOF] ([classof_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[CLASSOF_SEMESTER] CHECK CONSTRAINT [FK_CLASSOF_SEMESTER_CLASSOF]
GO
/****** Object:  ForeignKey [FK_TMP_SELECTION_CLASSOF]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[TMP_SELECTION]  WITH CHECK ADD  CONSTRAINT [FK_TMP_SELECTION_CLASSOF] FOREIGN KEY([classof_id])
REFERENCES [dbo].[CLASSOF] ([classof_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[TMP_SELECTION] CHECK CONSTRAINT [FK_TMP_SELECTION_CLASSOF]
GO
/****** Object:  ForeignKey [FK_TMP_SELECTION_STUDENT]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[TMP_SELECTION]  WITH CHECK ADD  CONSTRAINT [FK_TMP_SELECTION_STUDENT] FOREIGN KEY([student_id])
REFERENCES [dbo].[STUDENT] ([student_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[TMP_SELECTION] CHECK CONSTRAINT [FK_TMP_SELECTION_STUDENT]
GO
/****** Object:  ForeignKey [FK_TMP_SELECTION_SUBJECT]    Script Date: 01/12/2015 23:44:56 ******/
ALTER TABLE [dbo].[TMP_SELECTION]  WITH CHECK ADD  CONSTRAINT [FK_TMP_SELECTION_SUBJECT] FOREIGN KEY([subject_id])
REFERENCES [dbo].[SUBJECT] ([subject_id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[TMP_SELECTION] CHECK CONSTRAINT [FK_TMP_SELECTION_SUBJECT]
GO



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

