INSERT INTO `menu` (`ID`, `MENU_NAME`, `PERMALINK`, `MENU_ICON`, `MENU_ORDER`, `STATUS`, `CREATE_DATE`, `UPDATE_DATE`, `MENU_ID`) VALUES
(1, 'Setup Menu', '#', 'bars', '91', '1', '2019-01-08 15:51:57', '2019-01-17 09:56:06', NULL),
(2, 'User & Role', '#', 'users-cog', '92', '1', '2019-01-08 15:52:58', '2019-01-17 09:56:25', NULL),
(3, 'Application Data', 'app_data', 'cogs', '93', '1', '2019-01-08 15:54:30', '2019-01-17 09:56:38', NULL),
(4, 'List Menu', 'menu', 'bars', '9101', '1', '2019-01-08 15:55:15', '2019-01-17 09:56:13', 1),
(5, 'Assign Menu', 'assignmenu', 'bar', '9102', '1', '2019-01-08 15:56:23', '2019-01-17 09:56:18', 1),
(6, 'List User', 'user', '', '9201', '1', '2019-01-08 15:57:31', '2019-01-17 09:56:31', 2),
(7, 'List Role', 'role', '', '9202', '1', '2019-01-08 15:57:57', '2019-01-17 09:56:34', 2);

INSERT INTO `role_menu` (`ID`, `ROLE_ID`, `MENU_ID`) VALUES
(1, 1, 1),
(2, 1, 4),
(3, 1, 5),
(4, 1, 2),
(5, 1, 6),
(6, 1, 7),
(7, 1, 3);