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
 * @package    Enterprise_TargetRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Enterprise_TargetRule_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('catalog_product', 'related_targetrule_rule_based_positions', array(
    'group'        => 'General',
    'label'        => 'Related Target Rule Rule Based Positions',
    'visible'      => false,
    'user_defined' => false,
    'required'     => false,
    'type'         => 'int',
    'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'        => 'text',
));

$installer->addAttribute('catalog_product', 'related_targetrule_position_behavior', array(
    'group'        => 'General',
    'label'        => 'Related Target Rule Position Behavior',
    'visible'      => false,
    'user_defined' => false,
    'required'     => false,
    'type'         => 'int',
    'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'        => 'text',
));

$installer->addAttribute('catalog_product', 'upsell_targetrule_rule_based_positions', array(
    'group'        => 'General',
    'label'        => 'Upsell Target Rule Rule Based Positions',
    'visible'      => false,
    'user_defined' => false,
    'required'     => false,
    'type'         => 'int',
    'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'        => 'text',
));


$installer->addAttribute('catalog_product', 'upsell_targetrule_position_behavior', array(
    'group'        => 'General',
    'label'        => 'Upsell Target Rule Position Behavior',
    'visible'      => false,
    'user_defined' => false,
    'required'     => false,
    'type'         => 'int',
    'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'        => 'text',
));

$installer->addAttribute('catalog_product', 'crosssell_targetrule_rule_based_positions', array(
    'group'        => 'General',
    'label'        => 'Crosssell Target Rule Rule Based Positions',
    'visible'      => false,
    'user_defined' => false,
    'required'     => false,
    'type'         => 'int',
    'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'        => 'text',
));

$installer->addAttribute('catalog_product', 'crosssell_targetrule_position_behavior', array(
    'group'        => 'General',
    'label'        => 'Crosssell Target Rule Position Behavior',
    'visible'      => false,
    'user_defined' => false,
    'required'     => false,
    'type'         => 'int',
    'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'input'        => 'text',
));

$installer->endSetup();
