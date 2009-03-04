<?php
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Portero
 * @package    Portero_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;


$installer->startSetup();


$installer->run("
CREATE TABLE `{$installer->getTable('invitation_invitation')}` (
    `invitation_id` INT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `customer_id` INT( 10 ) UNSIGNED DEFAULT NULL ,
    `date` DATETIME NOT NULL ,
    `email` VARCHAR( 255 ) NOT NULL ,
    `referral_id` INT( 10 ) UNSIGNED DEFAULT NULL ,
    `protection_code` CHAR(32) NOT NULL,
    `signup_date` DATETIME DEFAULT NULL,
    `store_id` SMALLINT(5) UNSIGNED NOT NULL,
    `group_id` SMALLINT(3) UNSIGNED NOT NULL,
    `message` TEXT DEFAULT NULL,
    `status` ENUM('sent','accepted', 'canceled') NOT NULL,
    INDEX `customer_id` (`customer_id`),
    INDEX `referral_id` (`referral_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'INVITATION_STORE',
    $installer->getTable('invitation_invitation'),
    'store_id',
    $installer->getTable('core_store'),
    'store_id'
);

$installer->run("
CREATE TABLE `{$installer->getTable('invitation_invitation_status_history')}` (
    `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `invitation_id` INT UNSIGNED NOT NULL,
    `date` DATETIME NOT NULL,
    `status` ENUM('sent','accepted', 'canceled') NOT NULL,
    INDEX `invitation_id` (`invitation_id`)
) ENGINE=InnoDB;
");

$installer->getConnection()->addConstraint(
    'INVITATION_HISTORY_INVITATION',
    $installer->getTable('invitation_invitation_status_history'),
    'invitation_id',
    $installer->getTable('invitation_invitation'),
    'invitation_id'
);

$installer->endSetup();