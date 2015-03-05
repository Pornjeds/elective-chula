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
( '5682221820', 1, N'เอี่ยว', N'นามสมมติ', 'eaw@gmail.com','passwd01','profilepic',3.00, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221821', 1, N'บอย', N'นามสมมติ', 'boy@gmail.com','passwd01','profilepic',3.10, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221822', 1, N'กี้', N'นามสมมติ', 'gie@gmail.com','passwd01','profilepic',3.20, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221823', 1, N'เบียร์', N'นามสมมติ', 'beer@gmail.com','passwd01','profilepic',3.30, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221824', 1, N'ลิง', N'นามสมมติ', 'ling@gmail.com','passwd01','profilepic',3.40, GETDATE(), GETDATE(), 0 )
INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES 
( '5682221825', 1, N'ยงค์', N'นามสมมติ', 'yong@gmail.com','passwd01','profilepic',3.50, GETDATE(), GETDATE(), 0 )

/* [eaw01].[dbo].[[SUBJECT]]  */
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 1, N'OPERATION MANAGEMENT', N'description OM' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 2, N'FINANCIAL MANAGEMENT', N'description FM' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 3, N'MARKETING MANAGEMENT', N'description MM' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 4, N'ORGANICATION BEHAVIOUR', N'description OB' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 5, N'INTERFINANCIAL', N'description INTER FIN' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 6, N'ENTREPRENUR', N'description ENTRE' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 7, N'INTERFINANCIAL2', N'description INTER FIN' , 3, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT] ([subject_id],[name] ,[description] ,[defaultpoint] ,[addeddate] ,[updatedate]) VALUES
( 8, N'ENTREPRENUR2', N'description ENTRE' , 3, GETDATE(), GETDATE() )

/* [eaw01].[dbo].[[SUBJECT_CLASSOF]]  */
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 1, 1, '4' , 3, 6, 3, 1, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 2, 1, '4' , 0, 4, 3, 2, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 3, 1, '4' , 4, 5, 3, 2, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 4, 1, '4' , 0, 10, 3, 3, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 5, 1, '4' , 4, 5, 3, 4, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[SUBJECT_CLASSOF] ([subject_id],[classof_id] ,[semester] ,[minstudent] ,[maxstudent] ,[credit], [dayofweek], [timeofday], [instructor], [isRequired], [addeddate], [updatedate]) VALUES
( 6, 1, '4' , 3, 5, 3, 5, 2, 'Dr. A BCD', 0, GETDATE(), GETDATE() )

/* [eaw01].[dbo].[[CLASSOF_SEMESTER]]  */
INSERT INTO [eaw01].[dbo].[CLASSOF_SEMESTER] ([classof_id] ,[semester] ,[mincredit] ,[maxcredit] ,[pickmethod_id], [addeddate], [updatedate]) VALUES
( 1, '4' , 6, 9, 3, GETDATE(), GETDATE() )

/* [eaw01].[dbo].[[PICKMETHOD]]  */
INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES
( 'First Come First Serve' , GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES
( 'Sorting By GPA' , GETDATE(), GETDATE() )
INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES
( 'Random out by rank' , GETDATE(), GETDATE() )

/* [eaw01].[dbo].[[ADMIN_MEMBER]]  */
INSERT INTO [eaw01].[dbo].[ADMIN_MEMBER] ([name], [lastname], [email], [password], [addeddate]) VALUES
(N'Admin A', N'a', 'admina@admin.com', '123456', GETDATE())
INSERT INTO [eaw01].[dbo].[ADMIN_MEMBER] ([name], [lastname], [email], [password], [addeddate]) VALUES
(N'Admin B', N'b', 'adminb@admin.com', '123456', GETDATE())
INSERT INTO [eaw01].[dbo].[ADMIN_MEMBER] ([name], [lastname], [email], [password], [addeddate]) VALUES
(N'Admin C', N'c', 'adminc@admin.com', '123456', GETDATE())
INSERT INTO [eaw01].[dbo].[ADMIN_MEMBER] ([name], [lastname], [email], [password], [addeddate]) VALUES
(N'Admin d', N'd', 'admind@admin.com', '123456', GETDATE())

/* [eaw01].[dbo].[[USER_ROLE]] */
INSERT INTO [eaw01].[dbo].[USER_ROLE] ([user_id], [role], [updatedate]) VALUES
('1', 'Admin', GETDATE())
INSERT INTO [eaw01].[dbo].[USER_ROLE] ([user_id], [role], [updatedate]) VALUES
('2', 'Admin', GETDATE())
INSERT INTO [eaw01].[dbo].[USER_ROLE] ([user_id], [role], [updatedate]) VALUES
('5682221822', 'Admin', GETDATE())
INSERT INTO [eaw01].[dbo].[USER_ROLE] ([user_id], [role], [updatedate]) VALUES
('5682221824', 'Admin', GETDATE())

/* [eaw01].[dbo].[[STUDENT_ENROLLMENT]] */
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221820',1,1,'4',1,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221821',1,1,'4',2,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221822',1,1,'4',3,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221823',1,1,'4',4,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221824',1,1,'4',5,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221825',1,1,'4',6,GETDATE())

INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221820',2,1,'4',6,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221821',2,1,'4',5,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221822',2,1,'4',4,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221823',2,1,'4',3,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221824',2,1,'4',2,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221825',2,1,'4',1,GETDATE())

INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221820',3,1,'4',5,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221821',3,1,'4',4,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221822',3,1,'4',6,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221823',3,1,'4',1,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221824',3,1,'4',3,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221825',3,1,'4',2,GETDATE())


INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221820',4,1,'4',2,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221821',4,1,'4',3,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221822',4,1,'4',1,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221823',4,1,'4',6,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221824',4,1,'4',4,GETDATE())
INSERT INTO [eaw01].[dbo].[STUDENT_ENROLLMENT] ([student_id], [subject_id], [classof_id], [semester], [priority], [addeddate]) VALUES 
('5682221825',4,1,'4',5,GETDATE())



