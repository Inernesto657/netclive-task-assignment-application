-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2022 at 05:51 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `task_application`
--

-- --------------------------------------------------------

--
-- Table structure for table `assigned_tasks`
--

CREATE TABLE `assigned_tasks` (
  `id` int(3) NOT NULL,
  `taskId` int(3) NOT NULL,
  `taskName` varchar(255) NOT NULL,
  `taskDescription` text NOT NULL,
  `assignor` varchar(255) NOT NULL,
  `assignee` varchar(255) NOT NULL,
  `assigneeHierarchicalValue` int(3) NOT NULL,
  `assigneeDepartment` varchar(255) NOT NULL DEFAULT 'nil',
  `status` varchar(255) NOT NULL DEFAULT 'in progress',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assigned_tasks`
--

INSERT INTO `assigned_tasks` (`id`, `taskId`, `taskName`, `taskDescription`, `assignor`, `assignee`, `assigneeHierarchicalValue`, `assigneeDepartment`, `status`, `createdAt`, `updatedAt`) VALUES
(21, 32, 'Account Authorization', 'authorize the accounts for procurement of new equipements', 'ernest', 'ernest.inyama@netclive.com', 1, 'nil', 'in progress', '2022-10-21 15:39:03', '2022-10-21 15:39:03'),
(22, 33, 'Meeting Department Managers', 'compulsory meeting with department managers on the 30th of october', 'ernest', 'ernest.inyama@netclive.com', 1, 'nil', 'in progress', '2022-10-21 15:39:27', '2022-10-21 15:39:27'),
(23, 28, 'Manual Reset', 'reset the machine to manual standard', 'campbell', 'campbell.john@netclive.com', 2, 'production', 'completed', '2022-10-21 15:42:02', '2022-10-21 15:42:02'),
(26, 30, 'Machine 1 rpm', 'increase rpm for machine 1 to 20%', 'edwin', 'edwin.diaz@netclive.com', 3, 'production', 'completed', '2022-10-21 15:50:24', '2022-10-21 15:50:24');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(3) NOT NULL,
  `action` text NOT NULL,
  `department` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `action`, `department`, `time`) VALUES
(42, 'a task with id 28 was assigned by campbell.john@netclive.com and has been assigned to campbell.john@netclive.com', 'production', '2022-10-21 15:42:03'),
(43, 'a task with id 23 was completed by campbell.john@netclive.com', 'production', '2022-10-21 15:42:17'),
(44, 'a task with id 31 was assigned by campbell.john@netclive.com and has been assigned to maria.santa@netclive.com', 'production', '2022-10-21 15:42:57'),
(45, 'a task with id 30 was assigned by campbell.john@netclive.com and has been assigned to edwin.diaz@netclive.com', 'production', '2022-10-21 15:44:18'),
(46, 'a request to cancel a task assignment with task id 30 was made by campbell.john@netclive.com', 'production', '2022-10-21 15:44:35'),
(47, 'a request to cancel a task assignment with task id 31 was made by campbell.john@netclive.com', 'production', '2022-10-21 15:45:33'),
(48, 'a task request with task id 30 was approved by ernest.inyama@netclive.com', 'production', '2022-10-21 15:47:25'),
(49, 'a task request with task id 31 was approved by ernest.inyama@netclive.com', 'production', '2022-10-21 15:47:26'),
(50, 'a task assignment with task id 30 was cancelled by campbell.john@netclive.com', 'production', '2022-10-21 15:48:26'),
(51, 'a task assignment with task id 31 was cancelled by campbell.john@netclive.com', 'production', '2022-10-21 15:48:37'),
(52, 'a task with id 30 was assigned by edwin.diaz@netclive.com and has been assigned to edwin.diaz@netclive.com', 'production', '2022-10-21 15:50:24'),
(53, 'a task with id 26 was completed by edwin.diaz@netclive.com', 'production', '2022-10-21 15:50:44');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `hierarchicalValue` int(2) NOT NULL,
  `createdAt` date NOT NULL DEFAULT current_timestamp(),
  `updatedAt` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `hierarchicalValue`, `createdAt`, `updatedAt`) VALUES
(1, 'general manager', 1, '2022-10-03', '2022-10-03'),
(2, 'department manager', 2, '2022-10-03', '2022-10-03'),
(3, 'worker', 3, '2022-10-03', '2022-10-03');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `taskCartegory` varchar(255) NOT NULL,
  `taskCartegoryHierarchicalValue` int(3) NOT NULL,
  `department` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'unassigned',
  `createdAt` date NOT NULL DEFAULT current_timestamp(),
  `updatedAt` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `name`, `taskCartegory`, `taskCartegoryHierarchicalValue`, `department`, `description`, `status`, `createdAt`, `updatedAt`) VALUES
(24, 'Monthly Target', 'department manager', 2, 'sales', 'Minimize monthly target by around 2%', 'unassigned', '2022-10-21', '2022-10-21'),
(25, 'Advertising Channel', 'department manager', 2, 'sales', 'Increase the advertising in the media, including online channels', 'unassigned', '2022-10-21', '2022-10-21'),
(26, 'Sales Magazine', 'worker', 3, 'sales', 'Increase the number of magazines to 20%', 'unassigned', '2022-10-21', '2022-10-21'),
(27, 'credit and debit updation', 'worker', 3, 'sales', 'increase the credit and debit accounts for September', 'unassigned', '2022-10-21', '2022-10-21'),
(28, 'Manual Reset', 'department manager', 2, 'production', 'reset the machine to manual standard', 'assigned', '2022-10-21', '2022-10-21'),
(29, 'Annual Maintenance', 'department manager', 2, 'production', 'oversee and supervise annual maintenance on all machines on the 28th of October.', 'unassigned', '2022-10-21', '2022-10-21'),
(30, 'Machine 1 rpm', 'worker', 3, 'production', 'increase rpm for machine 1 to 20%', 'assigned', '2022-10-21', '2022-10-21'),
(31, 'Machine 2 rpm', 'worker', 3, 'production', 'increase rpm for machine 2 to 40%', 'unassigned', '2022-10-21', '2022-10-21'),
(32, 'Account Authorization', 'general manager', 1, 'nil', 'authorize the accounts for procurement of new equipements', 'assigned', '2022-10-21', '2022-10-21'),
(33, 'Meeting Department Managers', 'general manager', 1, 'nil', 'compulsory meeting with department managers on the 30th of october', 'assigned', '2022-10-21', '2022-10-21');

-- --------------------------------------------------------

--
-- Table structure for table `task_requests`
--

CREATE TABLE `task_requests` (
  `id` int(3) NOT NULL,
  `taskId` int(3) NOT NULL,
  `requester` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `taskDepartment` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `task_requests`
--

INSERT INTO `task_requests` (`id`, `taskId`, `requester`, `description`, `taskDepartment`, `status`, `createdAt`, `updatedAt`) VALUES
(9, 30, 'campbell.john@netclive.com', 'campbell.john@netclive.com has requested to cancel Machine 1 rpm task assignment', 'production', 'approved', '2022-10-21 15:44:35', '2022-10-21 15:44:35'),
(10, 31, 'campbell.john@netclive.com', 'campbell.john@netclive.com has requested to cancel Machine 2 rpm task assignment', 'production', 'approved', '2022-10-21 15:45:33', '2022-10-21 15:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(2) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `department` varchar(255) NOT NULL,
  `hierarchicalValue` int(2) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `email`, `password`, `department`, `hierarchicalValue`, `createdAt`, `updatedAt`) VALUES
(1, 'ernest', 'inyama', 'ernest.inyama@netclive.com', 'ernest123', 'nil', 1, '2022-09-28 04:00:00', '2022-10-21 11:47:33'),
(2, 'teja', 'bilal', 'teja.bilal@netclive.com', 'teja123', 'sales', 2, '2022-09-28 04:00:00', '2022-10-17 23:17:10'),
(3, 'francis', 'jacob', 'francis.jacob@netclive.com', 'francis123', 'production', 2, '2022-09-28 04:00:00', '2022-10-19 19:50:08'),
(4, 'campbell', 'john', 'campbell.john@netclive.com', 'campbell123', 'production', 2, '2022-09-28 04:00:00', '2022-10-21 11:48:52'),
(5, 'ramos', 'varane', 'ramos.varane@netclive.com', 'ramos123', 'production', 3, '2022-09-28 04:00:00', '2022-09-28 00:00:00'),
(6, 'edwin', 'diaz', 'edwin.diaz@netclive.com', 'edwin123', 'production', 3, '2022-09-28 04:00:00', '2022-09-28 00:00:00'),
(7, 'maria', 'santa', 'maria.santa@netclive.com', 'maria123', 'production', 3, '2022-09-28 04:00:00', '2022-09-28 00:00:00'),
(8, 'gonzales', 'sergio', 'gonzales.sergio@netclive.com', 'gonzales123', 'sales', 3, '2022-09-28 04:00:00', '2022-09-28 00:00:00'),
(9, 'mathew', 'paul', 'mathew.paul@netclive.com', 'mathew123', 'sales', 3, '2022-09-28 04:00:00', '2022-09-28 00:00:00'),
(10, 'martine', 'george', 'martine.george@netclive.com', 'martine123', 'sales', 3, '2022-09-28 04:00:00', '2022-09-28 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assigned_tasks`
--
ALTER TABLE `assigned_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_requests`
--
ALTER TABLE `task_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assigned_tasks`
--
ALTER TABLE `assigned_tasks`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `task_requests`
--
ALTER TABLE `task_requests`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
