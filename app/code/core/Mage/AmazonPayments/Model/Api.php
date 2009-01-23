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
    protected static $HMAC_SHA1_ALGORITHM = "sha1";

    public function getAmazonRedirectUrl()
    {
        $_url = Mage::getStoreConfig('payment/amazon_cba/redirect_url');
        $_merchantId = Mage::getStoreConfig('payment/amazon_cba/merchant_id');
        return $_url.$_merchantId;
    }

    /**
     * Computes RFC 2104-compliant HMAC signature.
     *
     * @param data Array
     *            The data to be signed.
     * @param key String
     *            The signing key, a.k.a. the AWS secret key.
     * @return The base64-encoded RFC 2104-compliant HMAC signature.
     */
    public function calculateSignature($data, $secretKey)
    {

        $stringData = '';
        foreach ($data as $key => $value) {
            $stringData .= $key.'='.rawurlencode($value).'&';
        }

        // compute the hmac on input data bytes, make sure to set returning raw hmac to be true
        $rawHmac = hash_hmac(self::$HMAC_SHA1_ALGORITHM, $stringData, $secretKey, true);

        // base64-encode the raw hmac
        return base64_encode($rawHmac);
    }

    /**
     *
     */
    public function getAmazonCbaOrderDetails($amazonOrderId)
    {
        $_merchantId = Mage::getStoreConfig('payment/amazon_cba/merchant_id');
        $options = array(
            'merchantIdentifier' => $_merchantId,
            'merchantName' => Mage::getStoreConfig('payment/amazon_cba/merchant_name'),
        );
        $_soap = $this->getSoapApi($options);

        $_options = array(
                'merchant'           => $_merchantId,
                'documentIdentifier' => $amazonOrderId,
            );

        /*$document = $_soap->getDocument($_options);
        echo '<pre> document:'."\n";
        print_r($document);
        echo '</pre>'."\n";*/
    }

    /**
     * Getting Soap Api object
     *
     * @param   array $options
     * @return  Mage_Cybersource_Model_Api_ExtendedSoapClient
     */
    protected function getSoapApi($options = array())
    {
        $wsdl = Mage::getBaseDir() . Mage::getStoreConfig('payment/amazon_cba/wsdl');
        return new Mage_AmazonPayments_Model_Api_ExtendedSoapClient($wsdl, $options);
    }
}