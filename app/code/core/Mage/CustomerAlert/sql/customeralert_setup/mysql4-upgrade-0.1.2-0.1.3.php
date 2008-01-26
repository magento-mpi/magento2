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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$this->run("
DROP TABLE IF EXISTS `customer_product_alert`;

CREATE TABLE `customer_product_alert` (
    `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `customer_id` INT( 11 ) UNSIGNED NOT NULL ,
    `product_id` INT( 11 ) UNSIGNED NOT NULL ,
    `store_id` INT( 11 ) UNSIGNED NOT NULL ,
    `type` VARCHAR( 255 ) NOT NULL
) ENGINE = InnoDB  DEFAULT CHARSET=utf8;';
");

