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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

// is price and related amounts are supposed to be calculated in purchase
$installer->addAttribute('catalog_product', 'is_price_nominal', array(
    'group'             => 'Prices',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Is Price Nominal',
    'note'              => 'Nominal price participates in catalog, but is not accounted for purchases.',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'simple,virtual',
    'is_configurable'   => false
));

// is product used for recurring payments
$installer->addAttribute('catalog_product', 'is_recurring', array(
    'group'             => 'Prices',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Is Recurring Payment',
    'note'              => 'Nominal price option may be used for manipulating recurring fees.',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'simple,virtual',
    'is_configurable'   => false
));

// recurring payment profile
$installer->addAttribute('catalog_product', 'recurring_profile', array(
    'group'             => 'Prices',
    'type'              => 'text',
    'backend'           => 'catalog/product_attribute_backend_recurring',
    'frontend'          => '',
    'label'             => 'Recurring Payment Profile',
    'input'             => 'text', // doesn't matter
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'simple,virtual',
    'is_configurable'   => false
));

