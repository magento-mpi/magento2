SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

ALTER TABLE `newsletter_template` DROP COLUMN `template_actual`;
ALTER TABLE `newsletter_template` ADD COLUMN `template_actual` TINYINT(1) UNSIGNED DEFAULT 1;

insert into `newsletter_template` (`template_id`,`template_code`,`template_text`,`template_text_preprocessed`,`template_type`,`template_subject`,`template_sender_name`,`template_sender_email`,`template_actual`) values ('','subscriberCodeConfirm','You have subscribed to Magento E-commerce Newsletter!<br />\r\n<br />\r\nTo confirm you subscription please follow this <a href=\"http://magento-mitch.kiev-dev/adminhtml/newsletter_template/{insvar%20subscriber.getConfirmationLink()}\" target=\"_blank\" title=\"Confirm subscription\">link</a>.<br />\r\n<br />\r\nThanks for your interest in our product!\r\n',NULL,2,'Confirmation of your subscription','Magento E-Commerce','core@magentocommerce.com',1);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
