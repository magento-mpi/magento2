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
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/* @var $installer Enterprise_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->run("
    CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_cms_widget_instance')}` (
      `instance_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `type` varchar(255) NOT NULL DEFAULT '',
      `package_theme` varchar(255) NOT NULL DEFAULT '',
      `title` varchar(255) NOT NULL DEFAULT '',
      PRIMARY KEY (`instance_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
