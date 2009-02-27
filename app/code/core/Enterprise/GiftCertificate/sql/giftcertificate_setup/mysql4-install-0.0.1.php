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
 * @category   Enterprise
 * @package    Enterprise_Permissions
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$installer->run('CREATE TABLE `' . $this->getTable('giftcertificate/giftcertificate') . '` (
                    `giftcertificate_id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY,
                    `code` varchar(50) NOT NULL,
                    `status` tinyint(1) NOT NULL,
                    `date_created` datetime NOT NULL,
                    `date_expires` datetime NOT NULL,
                    `website_id` smallint(5) NOT NULL
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

$installer->endSetup();
