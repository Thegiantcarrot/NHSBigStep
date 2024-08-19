-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 19, 2024 at 09:01 AM
-- Server version: 10.11.8-MariaDB-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u529174437_Walking`
--

-- --------------------------------------------------------

--
-- Table structure for table `GroupMembers`
--

CREATE TABLE `GroupMembers` (
  `GroupID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `GroupMembers`
--

INSERT INTO `GroupMembers` (`GroupID`, `Username`) VALUES
(21, 'AlexF'),
(21, 'Penguin'),
(22, '1'),
(22, '2'),
(22, 'AlexF'),
(22, 'MrPenguino'),
(23, '1'),
(23, '2'),
(23, 'AlexF');

-- --------------------------------------------------------

--
-- Table structure for table `GroupRequests`
--

CREATE TABLE `GroupRequests` (
  `RequestID` int(11) NOT NULL,
  `GroupID` int(11) DEFAULT NULL,
  `Requester` varchar(255) DEFAULT NULL,
  `RequestedUsername` varchar(255) DEFAULT NULL,
  `RequestDate` datetime DEFAULT current_timestamp(),
  `RequestedUser` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `GroupRequests`
--

INSERT INTO `GroupRequests` (`RequestID`, `GroupID`, `Requester`, `RequestedUsername`, `RequestDate`, `RequestedUser`) VALUES
(16, 16, '1', '2', '2024-08-13 13:04:05', ''),
(17, 16, '1', '2', '2024-08-13 13:05:33', '');

-- --------------------------------------------------------

--
-- Table structure for table `Groups`
--

CREATE TABLE `Groups` (
  `GroupID` int(11) NOT NULL,
  `GroupName` varchar(100) NOT NULL,
  `CreatedBy` varchar(50) DEFAULT NULL,
  `CreatorUsername` varchar(255) DEFAULT NULL,
  `Description` text NOT NULL,
  `Creator` varchar(255) NOT NULL,
  `CreationDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Groups`
--

INSERT INTO `Groups` (`GroupID`, `GroupName`, `CreatedBy`, `CreatorUsername`, `Description`, `Creator`, `CreationDate`) VALUES
(16, '1', NULL, '1', '1', '', '2024-08-13 17:47:58'),
(21, 'Se5 walkers', NULL, NULL, ':D', 'Penguin', '2024-08-13 17:47:58'),
(22, 'Mr Pinguino’s Group', NULL, NULL, 'Majestic Beaks only', 'MrPenguino', '2024-08-13 17:47:58'),
(23, 'Test', NULL, NULL, '', 'AlexF', '2024-08-16 14:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `GroupSteps`
--

CREATE TABLE `GroupSteps` (
  `Username` varchar(255) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `StepsToday` int(11) DEFAULT 0,
  `StepsTotal` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `GroupSteps`
--

INSERT INTO `GroupSteps` (`Username`, `GroupID`, `Date`, `StepsToday`, `StepsTotal`) VALUES
('Penguin', 21, '2024-08-13', 1000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `JoinRequests`
--

CREATE TABLE `JoinRequests` (
  `RequestID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Requester` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PasswordReset`
--

CREATE TABLE `PasswordReset` (
  `ResetID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `ResetToken` varchar(255) NOT NULL,
  `ExpiryDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `PasswordReset`
--

INSERT INTO `PasswordReset` (`ResetID`, `Email`, `ResetToken`, `ExpiryDate`) VALUES
(1, 'alexhfife@outlook.com', 'bc5346224fd804e0c026bae7074292ba1a488a52be1f73a8d220ab222cca67a8', '2024-08-13 15:10:33'),
(4, 'alexhfife@outlook.com', '887fbc32ff82f110a55aafe13139a0d7f0e5b493a6fe34228a54423c3f53fc87', '2024-08-16 11:01:13');

-- --------------------------------------------------------

--
-- Table structure for table `Requests`
--

CREATE TABLE `Requests` (
  `RequestID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `Requester` varchar(255) NOT NULL,
  `RequestedUsername` varchar(255) NOT NULL,
  `RequestDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Requests`
--

INSERT INTO `Requests` (`RequestID`, `GroupID`, `Requester`, `RequestedUsername`, `RequestDate`) VALUES
(10, 22, 'MrPenguino', '2', '2024-08-14 13:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `StepsForDay` int(11) DEFAULT 0,
  `StepsInTotal` int(11) DEFAULT 0,
  `steps_for_day` int(11) DEFAULT 0,
  `steps_in_total` int(11) DEFAULT 0,
  `Email` varchar(255) NOT NULL DEFAULT 'default@example.com',
  `LastUpdated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`UserID`, `Username`, `PasswordHash`, `StepsForDay`, `StepsInTotal`, `steps_for_day`, `steps_in_total`, `Email`, `LastUpdated`) VALUES
(1, 'Alex', '$2y$10$2HXPg7o.zmLQ1V18OfNHPuxq9rr2b7uW2f98w68nGXZGjN5dTkf12', 54800, 54824, 0, 0, 'default@example.com', '2024-08-13 17:48:39'),
(2, 'User1', '$2y$10$O4TD1kBSkgtxLU9ancizxu1bq0/R3bVuAK7ENJzKWTc40n2w9YzA.', 200, 5200, 0, 0, 'default@example.com', '2024-08-13 17:48:39'),
(3, '1', '$2y$10$cHD1dCsJpvhir8OqWTe8b.vE7j1EaWEMMIkn.HMxw1tD/kynoRmIe', 0, 4036, 0, 0, 'default@example.com', '2024-08-16 09:57:24'),
(4, '2', '$2y$10$nz6zojMYKyy8l2b2a1uyyOoYbuXNqlxKTIF1GHpK2gJWXxzTzQdYW', 102010, 117215, 0, 0, 'default@example.com', '2024-08-16 16:31:50'),
(5, 'AlexF', '$2y$10$sHXUjijuz6tVZ/wyHCkKxu85Y9YPL3BeK8G.zkakqRydXgNHveADO', 9300, 11300, 0, 0, 'default@example.com', '2024-08-16 18:08:19'),
(6, 'Penguin', '$2y$10$VWw/A6OJfR5hx.Pju95dk.KseBkcEX5tVzoqBCwg0V0yH6qwf5tPO', 2000, 9000, 0, 0, 'default@example.com', '2024-08-13 17:48:39'),
(7, '3', '$2y$10$UGNY1492Q/3JbonFiEZZ9O08Vcjb/ZuJvOQSyhXmEuzWcv80lNwUy', 0, 0, 0, 0, 'alexhfife@outlook.com', '2024-08-13 17:48:39'),
(8, 'MrPenguino', '$2y$10$D1vXo3m4DrEraO73etSdgemCgQ4MWFq52/HAPWogCuVTuHgciGzAy', 0, 19864, 0, 0, 'fifea@dulwich.org.uk', '2024-08-16 10:00:38'),
(9, '4', '$2y$10$4/r0S3L5iFD2f.DgqF7oXOioN1FlngzBWjNEdwdeo71syBf5sS0Fa', 0, 0, 0, 0, '4@3.com', '2024-08-16 16:38:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `GroupMembers`
--
ALTER TABLE `GroupMembers`
  ADD PRIMARY KEY (`GroupID`,`Username`),
  ADD UNIQUE KEY `GroupID` (`GroupID`,`Username`),
  ADD KEY `fk_user` (`Username`);

--
-- Indexes for table `GroupRequests`
--
ALTER TABLE `GroupRequests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `GroupID` (`GroupID`),
  ADD KEY `RequestedUsername` (`RequestedUsername`);

--
-- Indexes for table `Groups`
--
ALTER TABLE `Groups`
  ADD PRIMARY KEY (`GroupID`),
  ADD UNIQUE KEY `GroupName` (`GroupName`),
  ADD KEY `CreatedBy` (`CreatedBy`);

--
-- Indexes for table `GroupSteps`
--
ALTER TABLE `GroupSteps`
  ADD PRIMARY KEY (`Username`,`GroupID`,`Date`),
  ADD KEY `GroupID` (`GroupID`);

--
-- Indexes for table `JoinRequests`
--
ALTER TABLE `JoinRequests`
  ADD PRIMARY KEY (`RequestID`),
  ADD UNIQUE KEY `GroupID` (`GroupID`,`Username`),
  ADD KEY `fk_user_request` (`Username`);

--
-- Indexes for table `PasswordReset`
--
ALTER TABLE `PasswordReset`
  ADD PRIMARY KEY (`ResetID`);

--
-- Indexes for table `Requests`
--
ALTER TABLE `Requests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `GroupID` (`GroupID`),
  ADD KEY `Requester` (`Requester`),
  ADD KEY `RequestedUsername` (`RequestedUsername`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `GroupRequests`
--
ALTER TABLE `GroupRequests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `Groups`
--
ALTER TABLE `Groups`
  MODIFY `GroupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `JoinRequests`
--
ALTER TABLE `JoinRequests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `PasswordReset`
--
ALTER TABLE `PasswordReset`
  MODIFY `ResetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Requests`
--
ALTER TABLE `Requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `GroupMembers`
--
ALTER TABLE `GroupMembers`
  ADD CONSTRAINT `GroupMembers_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`) ON DELETE CASCADE,
  ADD CONSTRAINT `GroupMembers_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `Users` (`Username`),
  ADD CONSTRAINT `fk_group` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`),
  ADD CONSTRAINT `fk_group_members` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`Username`) REFERENCES `Users` (`Username`);

--
-- Constraints for table `GroupRequests`
--
ALTER TABLE `GroupRequests`
  ADD CONSTRAINT `GroupRequests_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`),
  ADD CONSTRAINT `GroupRequests_ibfk_2` FOREIGN KEY (`RequestedUsername`) REFERENCES `Users` (`Username`);

--
-- Constraints for table `Groups`
--
ALTER TABLE `Groups`
  ADD CONSTRAINT `Groups_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `Users` (`Username`);

--
-- Constraints for table `GroupSteps`
--
ALTER TABLE `GroupSteps`
  ADD CONSTRAINT `GroupSteps_ibfk_1` FOREIGN KEY (`Username`) REFERENCES `Users` (`Username`),
  ADD CONSTRAINT `GroupSteps_ibfk_2` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`) ON DELETE CASCADE;

--
-- Constraints for table `JoinRequests`
--
ALTER TABLE `JoinRequests`
  ADD CONSTRAINT `JoinRequests_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`),
  ADD CONSTRAINT `fk_group_request` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`),
  ADD CONSTRAINT `fk_join_requests` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_request` FOREIGN KEY (`Username`) REFERENCES `Users` (`Username`);

--
-- Constraints for table `Requests`
--
ALTER TABLE `Requests`
  ADD CONSTRAINT `Requests_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`),
  ADD CONSTRAINT `Requests_ibfk_2` FOREIGN KEY (`Requester`) REFERENCES `Users` (`Username`),
  ADD CONSTRAINT `Requests_ibfk_3` FOREIGN KEY (`RequestedUsername`) REFERENCES `Users` (`Username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
