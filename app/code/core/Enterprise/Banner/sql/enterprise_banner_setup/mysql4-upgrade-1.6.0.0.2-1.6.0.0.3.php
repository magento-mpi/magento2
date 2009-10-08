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
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Enterprise_Banner_Model_Mysql4_Setup */
$installer->startSetup();

$bannerCustomerSegmentTable = $installer->getTable('enterprise_banner/customersegment');

$installer->run("CREATE TABLE `{$bannerCustomerSegmentTable}` (
 `banner_id` int(10) unsigned NOT NULL default '0',
 `segment_id` int(10) unsigned default NULL,
 KEY `banner_id` (`banner_id`),
 KEY `segment_id` (`segment_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation Banners with Customer Segments'");

$installer->getConnection()->addConstraint(
    'FK_BANNER_CUSTOMER_SEGMENT_BANNER', $bannerCustomerSegmentTable, 'banner_id',
    $installer->getTable('enterprise_banner/banner'), 'banner_id', 'CASCADE', 'CASCADE'
);
$installer->getConnection()->addConstraint(
    'FK_BANNER_CUSTOMER_SEGMENT_SEGMENT', $bannerCustomerSegmentTable, 'segment_id',
    $installer->getTable('enterprise_customersegment/segment'), 'segment_id', 'CASCADE', 'CASCADE'
);

$installer->run("ALTER TABLE `{$bannerCustomerSegmentTable}` ADD PRIMARY KEY ( `banner_id` , `segment_id` )");
