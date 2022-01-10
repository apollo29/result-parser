CREATE TABLE `verband_sfv` (
  `id` int(11) NOT NULL,
  `verbandsname` varchar(255) NOT NULL,
  `verband` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `verband_sfv`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `verband` (`verband`);

ALTER TABLE `verband_sfv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
COMMIT;
