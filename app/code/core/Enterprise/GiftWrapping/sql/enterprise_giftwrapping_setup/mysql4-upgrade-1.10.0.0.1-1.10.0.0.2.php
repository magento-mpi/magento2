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
 * @package     Enterprise_GiftWrapping
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Enterprise_GiftWrapping_Model_Resource_Mysql4_Setup */

$installer->addAttribute('quote', 'giftwrapping_options', array('type' => 'text', 'visible' => false));
$installer->addAttribute('quote_item', 'giftwrapping_options', array('type' => 'text', 'visible' => false));

$installer->addAttribute('quote_address', 'giftwrapping_options', array('type' => 'text', 'visible' => false));
$installer->addAttribute('quote_address_item', 'giftwrapping_options', array('type' => 'text', 'visible' => false));

$installer->addAttribute('order', 'giftwrapping_options', array('type' => 'text', 'visible' => false));
$installer->addAttribute('order_item', 'giftwrapping_options', array('type' => 'text', 'visible' => false));