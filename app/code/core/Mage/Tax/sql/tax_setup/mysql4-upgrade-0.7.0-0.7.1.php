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
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Rate SQL upgrade
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author     Victor Tihonchuk <victor.tihonchuk@varien.com>
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
    ALTER TABLE `tax_rate_data` DROP FOREIGN KEY `FK_TAX_RATE_DATA_TAX_RATE`;
    ALTER TABLE `tax_rate` CHANGE `tax_rate_id` `tax_rate_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
    ALTER TABLE `tax_rate_data` CHANGE `tax_rate_data_id` `tax_rate_data_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
    ALTER TABLE `tax_rate_data` CHANGE `tax_rate_id` `tax_rate_id` INT UNSIGNED NOT NULL DEFAULT '0';
    ALTER TABLE `tax_rate_data` ADD CONSTRAINT `FK_TAX_RATE_DATA_TAX_RATE` FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rate` (`tax_rate_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");
$installer->endSetup();