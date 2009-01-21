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
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AmazonPayments API wrappers model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api extends Mage_AmazonPayments_Model_Api_Abstract
{

    public function getAmazonRedirectUrl()
    {
        return Mage::getStoreConfig('payment/amazon_cba/redirect_url');
    }

    /**
     * SetCbaCheckout API call
     *
     * An express checkout transaction starts with a token, that
     * identifies to PayPal your transaction
     * In this example, when the script sees a token, the script
     * knows that the buyer has already authorized payment through
     * paypal.  If no token was found, the action is to send the buyer
     * to PayPal to first authorize payment
     */
    public function callSetCbaCheckout()
    {
        //------------------------------------------------------------------------------------------------------------------------------------
        // Construct the parameter string that describes the SetExpressCheckout API call

        $nvpArr = array(
            'PAYMENTACTION' => $this->getPaymentType(),
            'AMT'           => $this->getAmount(),
            'CURRENCYCODE'  => $this->getCurrencyCode(),
            'RETURNURL'     => $this->getReturnUrl(),
            'CANCELURL'     => $this->getCancelUrl(),
            'INVNUM'        => $this->getInvNum()
        );

        if ($this->getPageStyle()) {
            $nvpArr = array_merge($nvpArr, array(
                'PAGESTYLE' => $this->getPageStyle()
            ));
        }

        $this->setUserAction(self::USER_ACTION_CONTINUE);

        // for mark SetExpressCheckout API call
        if ($a = $this->getShippingAddress()) {
            $nvpArr = array_merge($nvpArr, array(
                'ADDROVERRIDE'      => 1,
                'SHIPTONAME'        => $a->getName(),
                'SHIPTOSTREET'      => $a->getStreet(1),
                'SHIPTOSTREET2'     => $a->getStreet(2),
                'SHIPTOCITY'        => $a->getCity(),
                'SHIPTOSTATE'       => $a->getRegionCode(),
                'SHIPTOCOUNTRYCODE' => $a->getCountry(),
                'SHIPTOZIP'         => $a->getPostcode(),
                'PHONENUM'          => $a->getTelephone(),
            ));
            $this->setUserAction(self::USER_ACTION_COMMIT);
        }

        //'---------------------------------------------------------------------------------------------------------------
        //' Make the API call to PayPal
        //' If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
        //' If an error occured, show the resulting errors
        //'---------------------------------------------------------------------------------------------------------------
        $resArr = $this->call('SetExpressCheckout', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setToken($resArr['TOKEN']);
        $this->setRedirectUrl($this->getPaypalUrl());
        return $resArr;
    }
}