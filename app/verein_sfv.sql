CREATE TABLE `verein_sfv` (
  `vereinsnummer` int(11) NOT NULL,
  `vereinsname` varchar(255) NOT NULL,
  `verband` varchar(10) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `verein_sfv`
  ADD PRIMARY KEY (`vereinsnummer`),
  ADD KEY `verband` (`verband`);
COMMIT;
