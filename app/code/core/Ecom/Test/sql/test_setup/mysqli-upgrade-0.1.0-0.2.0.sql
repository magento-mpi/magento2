ALTER TABLE `test` ADD COLUMN `field2` text NULL;
INSERT INTO `test` SET `field1`='test', `field2`='Test\r\nrwar';