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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * AdminNotification update frequency source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Victor Tihonchuk <victor@varien.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Notification_Frequency
{
    public function toOptionArray()
    {
        return array(
            1   => Mage::helper('adminhtml')->__('1 Hour'),
            2   => Mage::helper('adminhtml')->__('2 Hours'),
            6   => Mage::helper('adminhtml')->__('6 Hours'),
            12  => Mage::helper('adminhtml')->__('12 Hours'),
            24  => Mage::helper('adminhtml')->__('24 Hours')
        );
    }
}