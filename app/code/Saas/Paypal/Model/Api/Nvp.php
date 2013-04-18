<?php
/**
 * Magento Saas Edition
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
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Saas_Paypal_Model_Api_Nvp extends Mage_Paypal_Model_Api_Nvp
{
    /**
     * Internal constructor
     * Change global map SUBJECT code
     */
    protected function _construct()
    {
        $this->_globalMap['SUBJECT'] = 'boarding_account';
        array_push($this->_eachCallRequest, 'SUBJECT');
    }

    /**
     * PayPal tenant merchant email getter
     *
     * @return string
     */
    public function getBoardingAccount()
    {
        if ($this->_getDataOrConfig('receiver_id')) {
            return $this->_getDataOrConfig('receiver_id');
        }
        return $this->_getDataOrConfig('boarding_account');
    }

    /**
     * Do not remove SUBJECT field from request
     *
     * @param &array $requestFields
     */
    protected function _prepareExpressCheckoutCallRequest(&$requestFields)
    {
        //nothing to do
    }
}
