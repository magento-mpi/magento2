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
 * Adminhtml backend model for "Use Secure URLs in Admin" option
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Victor Tihonchuk <victor@varien.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Usecustom extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {

        $value = $this->getValue();
        if ($value == 1) {
            $customUrl = $this->getData('groups/url/fields/custom/value');
            if (empty($customUrl)) {
                Mage::throwException(Mage::helper('adminhtml')->__('Please specify admin custom URL'));
            }
        }

        return $this;
    }
}