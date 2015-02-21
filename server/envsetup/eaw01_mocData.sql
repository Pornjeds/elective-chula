USE [eaw01]
GO

/* Empty table */
truncate table dbo.ADMIN_AUDITLOG
truncate table dbo.STUDENT_CONFIRMED_ENROLLMENT
truncate table dbo.STUDENT_ENROLLMENT
truncate table dbo.SUBJECT_CLASSOF
truncate table dbo.CLASSOF_SEMESTER
truncate table dbo.TMP_SELECTION


Delete from dbo.CLASSOF
Delete from dbo.SUBJECT
Delete from dbo.STUDENT
Delete from dbo.ADMIN_MEMBER
Delete from dbo.USER_ROLE

/* [eaw01].[dbo].[[CLASSOF]]  */
INSERT INTO [eaw01].[dbo].[CLASSOF] ([classof_description]) VALUES ( '19/1' )
INSERT INTO [eaw01].[dbo].[CLASSOF] ([classof_description]) VALUES ( '19/2' )
INSERT INTO [eaw01].[dbo].[CLASSOF] ([classof_description]) VALUES ( '20/1' )
INSERT INTO [eaw01].[dbo].[CLASSOF] ([classof_description]) VALUES ( '20/2' )
INSERT INTO [eaw01].[dbo].[CLASSOF] ([classof_description]) VALUES ( '21/1' )
INSERT INTO [eaw01].[dbo].[CLASSOF] ([classof_description]) VALUES ( '21/2' )

/* [eaw01].[dbo].[STUDENT]  */
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221820', 1, 'เอี่ยว', 'นามสมมติ', 'eaw@gmail.com','passwd01','profilepic',3.00, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221821', 1, 'บอย', 'นามสมมติ', 'boy@gmail.com','passwd01','profilepic',3.10, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221822', 1, 'กี้', 'นามสมมติ', 'gie@gmail.com','passwd01','profilepic',3.20, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221823', 1, 'เบียร์', 'นามสมมติ', 'beer@gmail.com','passwd01','profilepic',3.30, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221824', 1, 'ลิง', 'นามสมมติ', 'ling@gmail.com','passwd01','profilepic',3.40, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221825', 1, 'ยงค์', 'นามสมมติ', 'yong@gmail.com','passwd01','profilepic',3.50, GETDATE(), GETDATE(), 0 )

/* [eaw01].[dbo].[[SUBJECT]]  */
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 1, 'OPERATION MANAGEMENT', 'description OM' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 2, 'FINANCIAL MANAGEMENT', 'description FM' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 3, 'MARKETING MANAGEMENT', 'description MM' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 4, 'ORGANICATION BEHAVIOUR', 'description OB' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 5, 'INTERFINANCIAL', 'description INTER FIN' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 6, 'ENTREPRENUR', 'description ENTRE' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 7, 'INTERFINANCIAL2', 'description INTER FIN' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 8, 'ENTREPRENUR2', 'description ENTRE' , 3, GETDATE(), GETDATE() )

/* [eaw01].[dbo].[[SUBJECT_CLASSOF]]  */
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 1, 1, '4' , 10, 40, 3, 1, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 2, 1, '4' , 10, 40, 3, 1, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 3, 1, '4' , 10, 40, 3, 1, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 4, 1, '4' , 10, 40, 3, 1, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 5, 1, '4' , 10, 40, 3, 1, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 6, 1, '4' , 10, 40, 3, 1, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )

/* [eaw01].[dbo].[[CLASSOF_SEMESTER]]  */
INSERT INTO [eaw01].[dbo].[CLASSOF_SEMESTER] ([classof_id] ,[semester] ,[mincredit] ,[maxcredit] ,[pickmethod_id], [addeddate], [updatedate]) VALUES
( 1, '4' , 6, 9, 0, GETDATE(), GETDATE() )

/* [eaw01].[dbo].[[PICKMETHOD]]  */
INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES
( 'First Come First Serve' , GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES
( 'Sorting By GPA' , GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES
( 'Random out by rank' , GETDATE(), GETDATE() )



