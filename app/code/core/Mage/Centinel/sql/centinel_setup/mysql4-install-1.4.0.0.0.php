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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Mage_Centinel_Model_Mysql4_Setup */
$installer = $this;

$installer->getConnection()->delete($installer->getTable('cms/page'), '`identifier` = \'centinel-verified-by-visa\'');

$now = new Zend_Date(time());
$now = $now->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
$installer->getConnection()->insert($installer->getTable('cms/page'), array(
    'title'           => 'Verified By Visa',
    'root_template'   => 'empty',
    'identifier'      => 'centinel-verified-by-visa',
    'content_heading' => 'Verified By Visa',
    'creation_time'   => $now,
    'update_time'     => $now,
    'is_active'       => 1,
    'content' => '

    CONTENT FROM <a href="https://www.hotwire.com/pop-up/verified-by-visa.jsp">HERE</a>

    ',
));

$pageId = $installer->getConnection()->lastInsertId();
$installer->getConnection()->insert(
    $installer->getTable('cms/page_store'), array('page_id' => $pageId, 'store_id' => 0)
);

