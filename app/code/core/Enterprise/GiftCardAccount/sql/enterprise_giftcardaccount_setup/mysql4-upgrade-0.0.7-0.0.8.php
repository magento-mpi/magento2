<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Enterprise_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('enterprise_giftcardaccount_pool')}` (
    `code` varchar(255) NOT NULL PRIMARY KEY,
    `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->changeColumn(
    $this->getTable('enterprise_giftcardaccount'),
    'code',
    'code',
    'VARCHAR(255) NOT NULL'
);

$installer->endSetup();
