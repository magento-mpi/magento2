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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$versionTable = $installer->getTable('enterprise_cms/page_version');
$revisionTable = $installer->getTable('enterprise_cms/page_revision');
$pageTable = $installer->getTable('cms/page');

$installer->getConnection()->addColumn($pageTable, 'published_revision_id', ' int(10) unsigned default NULL');

/*
 * Updating new created column with values
 */
$select = 'UPDATE e_cms_page as p
SET published_revision_id = (SELECT revision_id FROM
        e_enterprise_cms_page_version as v, e_enterprise_cms_page_revision as r
    WHERE v.page_id = p.page_id
        AND v.access_level = "' . Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC . '"
        AND r.version_id = v.version_id
        AND r.page_id = p.page_id ORDER BY revision_id DESC LIMIT 1)
WHERE p.published_revision_id is NULL';

$installer->getConnection()->query($select);

