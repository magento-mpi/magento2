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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer->removeAttribute('order', 'order_status_id');
$installer->addAttribute('order', 'state', array('type'=>'varchar', 'visible'=>false));
$installer->addAttribute('order', 'status', array('type'=>'varchar', 'visible'=>false));

$installer->removeAttribute('order_status_history', 'comments');
$installer->addAttribute('order_status_history', 'status', array('type'=>'varchar', 'visible'=>false));
$installer->addAttribute('order_status_history', 'comment', array('type'=>'text', 'visible'=>false));

$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS `sales_order_status`;");
$installer->endSetup();
