-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: ictstu-db1.cc.swin.edu.au
-- Thời gian đã tạo: Th10 23, 2025 lúc 12:51 PM
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
-- Cấu trúc bảng cho bảng `Position`
--

CREATE TABLE `Position` (
  `Position_ID` int(50) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `Position`
--

INSERT INTO `Position` (`Position_ID`, `DepartmentID`, `Title`, `Description`) VALUES
(1001, 100, 'HR Manager', 'Oversees all human resource functions and strategies.'),
(1002, 100, 'Talent Acquisition Specialist', 'Manages the full-cycle recruitment process.'),
(1003, 100, 'Compensation & Benefits Analyst', 'Analyzes and administers employee compensation and benefits programs.'),
(1004, 100, 'Training & Development Coordinator', 'Designs and implements employee training programs.'),
(1005, 100, 'HR Generalist', 'Handles day-to-day HR duties and employee relations.'),
(1006, 100, 'HR Intern', 'Assists the HR team with administrative tasks.'),
(2001, 200, 'Chief Marketing Officer (CMO)', 'Leads all marketing initiatives and strategic direction.'),
(2002, 200, 'Digital Marketing Specialist', 'Manages online campaigns, SEO/SEM, and social media.'),
(2003, 200, 'Content Creator', 'Produces engaging written and visual content for various channels.'),
(2004, 200, 'Market Research Analyst', 'Analyzes market trends, customer behavior, and competitive landscape.'),
(2005, 200, 'Product Marketing Manager', 'Focuses on promoting specific products to target markets.'),
(2006, 200, 'Marketing Intern', 'Supports the marketing team in campaign execution and reporting.'),
(3001, 300, 'IT Director', 'Oversees all technology infrastructure and IT staff.'),
(3002, 300, 'Software Engineer', 'Develops, tests, and maintains software applications.'),
(3003, 300, 'Network Administrator', 'Manages and maintains the company network and servers.'),
(3004, 300, 'Cybersecurity Analyst', 'Protects systems and data from cyber threats.'),
(3005, 300, 'Data Analyst', 'Collects, processes, and performs statistical analysis on data.'),
(3006, 300, 'IT Helpdesk Technician', 'Provides technical support to employees for hardware and software issues.'),
(4001, 400, 'Chief Financial Officer (CFO)', 'Responsible for managing the financial actions of a company.'),
(4002, 400, 'Senior Accountant', 'Manages general ledger, reconciliations, and financial closing.'),
(4003, 400, 'Auditor', 'Examines financial records to ensure compliance and accuracy.'),
(4004, 400, 'Payroll Specialist', 'Handles employee payroll processing and tax deductions.'),
(4005, 400, 'Treasury Analyst', 'Manages the company’s cash flow and funding needs.'),
(4006, 400, 'Accounts Payable Clerk', 'Processes vendor invoices and manages outgoing payments.'),
(5001, 500, 'Sales Director', 'Develops and executes strategic plan to achieve sales targets.'),
(5002, 500, 'Account Manager', 'Builds and maintains long-term relationships with key customers.'),
(5003, 500, 'Sales Representative', 'Generates leads, qualifies prospects, and closes sales deals.'),
(5004, 500, 'Sales Operations Analyst', 'Optimizes sales processes and reporting efficiency.'),
(5005, 500, 'Business Development Associate (BDA)', 'Identifies new business opportunities and potential clients.'),
(5006, 500, 'Sales Intern', 'Supports the sales team with administrative and research tasks.');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `Position`
--
ALTER TABLE `Position`
  ADD PRIMARY KEY (`Position_ID`),
  ADD KEY `fk_position_department` (`DepartmentID`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `Position`
--
ALTER TABLE `Position`
  ADD CONSTRAINT `fk_position_department` FOREIGN KEY (`DepartmentID`) REFERENCES `Department` (`DepartmentID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
