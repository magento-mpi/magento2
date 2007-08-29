/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

ALTER TABLE `admin_user` ADD `is_active` TINYINT(1) NOT NULL default '1';
TRUNCATE TABLE `admin_user`;
TRUNCATE TABLE `admin_role`;
TRUNCATE TABLE `admin_rule`;

#
# Default data for the `admin_role` table

INSERT INTO `admin_role` (`role_id`, `parent_id`, `tree_level`, `sort_order`, `role_type`, `user_id`, `role_name`) VALUES 
  (1,0,1,1,'G',0,'Administrators'),
  (2,1,2,1,'U',1,'Admin');

COMMIT;

#
# Default data for the `admin_rule` table

INSERT INTO `admin_rule` (`rule_id`, `role_id`, `resource_id`, `privileges`, `assert_id`, `role_type`, `permission`) VALUES 
  (1,1,'admin','',0,'G','allow');

COMMIT;

#
# Default data for the `admin_user` table

INSERT INTO `admin_user` (`user_id`, `firstname`, `lastname`, `email`, `username`, `password`, `is_active`, `created`, `modified`, `logdate`, `lognum`, `reload_acl_flag`) VALUES 
  (1,'Admin','User','admin@varien.com','admin','4297f44b13955235245b2497399d7a93',1,'2007-07-21','2007-07-21','2007-08-25 12:01:21',111,0);

COMMIT;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
