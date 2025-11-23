-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: ictstu-db1.cc.swin.edu.au
-- Thời gian đã tạo: Th10 23, 2025 lúc 12:52 PM
-- Phiên bản máy phục vụ: 5.5.68-MariaDB
-- Phiên bản PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `s105549964_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Department`
--

CREATE TABLE `Department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Location` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unknown',
  `ManagerEmployeeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Department`
--

INSERT INTO `Department` (`DepartmentID`, `DepartmentName`, `Location`, `ManagerEmployeeID`) VALUES
(100, 'HR', 'Room 1001, 10th Floor', 1001),
(200, 'Marketing', 'Room 101, 1st Floor', 1002),
(300, 'IT', 'Room 701, 7th Floor', 1003),
(400, 'Finance', 'Room 402, 4th Floor', 1004),
(500, 'Sales', 'Room 102, 1st Floor', 1005);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `Department`
--
ALTER TABLE `Department`
  ADD PRIMARY KEY (`DepartmentID`),
  ADD UNIQUE KEY `uq_department_name` (`DepartmentName`),
  ADD KEY `fk_department_manager` (`ManagerEmployeeID`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `Department`
--
ALTER TABLE `Department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `Department`
--
ALTER TABLE `Department`
  ADD CONSTRAINT `fk_department_manager` FOREIGN KEY (`ManagerEmployeeID`) REFERENCES `Employees7` (`EmployeeID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
