--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
CREATE TABLE `test` (
  `test_id` int(11) unsigned NOT NULL auto_increment,
  `field1` varchar(128) default NULL,
  PRIMARY KEY  (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='test table';

--
-- Dumping data for table `test`
--
