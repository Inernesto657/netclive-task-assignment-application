-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2022 at 02:00 AM
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
(1, 'ernest', 'inyama', 'ernest.inyama@netclive.com', 'ernest123', 'nil', 1, '2022-09-28 04:00:00', '2022-10-19 19:56:07'),
(2, 'teja', 'bilal', 'teja.bilal@netclive.com', 'teja123', 'sales', 2, '2022-09-28 04:00:00', '2022-10-17 23:17:10'),
(3, 'francis', 'jacob', 'francis.jacob@netclive.com', 'francis123', 'production', 2, '2022-09-28 04:00:00', '2022-10-19 19:50:08'),
(4, 'campbell', 'john', 'campbell.john@netclive.com', 'campbell123', 'production', 3, '2022-09-28 04:00:00', '2022-09-28 00:00:00'),
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
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `task_requests`
--
ALTER TABLE `task_requests`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
