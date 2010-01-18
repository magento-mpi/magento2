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
 * @package     Mage_Centinel
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create validation card block
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Centinel_Block_Adminhtml_Validation_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Return payment model
     *
     * @return Mage_Payment_Block_Info
     */
	protected function _getPayment()
    {
        return $this->getQuote()->getPayment();
    }

    /**
     * Return Centinel validator model
     *
     * @return Mage_Centinel_Model_Validator
     */
    protected function _getValidator()
    {
        $payment = $this->_getPayment();
        if ($payment->getMethod() && $payment->getMethodInstance()->getIsCentinelValidationEnabled()) {
            return $payment->getMethodInstance()->getCentinelValidator();
        }
        return false;
    }

    /**
     * Return flag - is centinel validation enabled 
     *
     * @return bool
     */
    public function isValidationEnabled()
    {
        return $this->_getValidator() != false;
    }

    /**
     * Return code of current payment method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_getPayment()->getMethod();
    }

    /**
     * Return url for payment validation request
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->_getValidator()->getValidationUrl();
    }

    /**
     * Return id of iframe container
     *
     * @return string
     */
    public function getContainerId()
    {
        return 'centinel_authenticate_iframe';
    }
}
