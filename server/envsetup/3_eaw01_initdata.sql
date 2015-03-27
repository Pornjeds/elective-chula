USE [eaw01]
GO

INSERT INTO [eaw01].[dbo].[CLASSOF] ([classof_description]) VALUES ( 'Admin')

INSERT INTO [eaw01].[dbo].[STUDENT] ([student_id],[classof_id],[name],[lastname],[email],[password],[profilepic],[GPA],[addeddate],[updatedate],[student_status]) VALUES ('admin',1,N'System',N'Admin','admin@ymba','3d2d8d6c2a7769dae876c245190b3ee06a5c3697','profilepic','4.00',GETDATE(),GETDATE(),0)

INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES ( N'First Come First Serve',GETDATE(),GETDATE())
INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES ( N'Sorting By GPA',GETDATE(),GETDATE())
INSERT INTO [eaw01].[dbo].[PICKMETHOD] ([name], [addeddate], [updatedate]) VALUES ( N'Random out by rank',GETDATE(),GETDATE())

INSERT INTO [eaw01].[dbo].[ADMIN_USERS] ([user_id], [role], [updatedate]) VALUES ('admin','Admin',GETDATE())