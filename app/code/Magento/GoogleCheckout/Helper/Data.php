<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleCheckout data helper
 */
namespace Magento\GoogleCheckout\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Google Checkout settings
     */
    const XML_PATH_REQUEST_PHONE                     = 'google/checkout/request_phone';
    const XML_PATH_DISABLE_DEFAULT_TAX_TABLES        = 'google/checkout/disable_default_tax_tables';

    /**
     * Google Checkout Shipping - Digital Delivery settings
     */
    const XML_PATH_SHIPPING_VIRTUAL_ACTIVE           = 'google/checkout_shipping_virtual/active';
    const XML_PATH_SHIPPING_VIRTUAL_SCHEDULE         = 'google/checkout_shipping_virtual/schedule';
    const XML_PATH_SHIPPING_VIRTUAL_METHOD           = 'google/checkout_shipping_virtual/method';

    /**
     * Google Checkout Shipping - Carrier Calculated settings
     */
    const XML_PATH_SHIPPING_CARRIER_ACTIVE           = 'google/checkout_shipping_carrier/active';
    const XML_PATH_SHIPPING_CARRIER_METHODS          = 'google/checkout_shipping_carrier/methods';
    const XML_PATH_SHIPPING_CARRIER_DEFAULT_PRICE    = 'google/checkout_shipping_carrier/default_price';
    const XML_PATH_SHIPPING_CARRIER_DEFAULT_WIDTH    = 'google/checkout_shipping_carrier/default_width';
    const XML_PATH_SHIPPING_CARRIER_DEFAULT_HEIGHT   = 'google/checkout_shipping_carrier/default_height';
    const XML_PATH_SHIPPING_CARRIER_DEFAULT_LENGTH   = 'google/checkout_shipping_carrier/default_length';
    const XML_PATH_SHIPPING_CARRIER_ADDRESS_CATEGORY = 'google/checkout_shipping_carrier/address_category';

    /**
     * Google Checkout Shipping - Flat Rate settings
     */
    const XML_PATH_SHIPPING_FLATRATE_ACTIVE          = 'google/checkout_shipping_flatrate/active';

    /**
     * Google Checkout Shipping - Merchant Calculated settings
     */
    const XML_PATH_SHIPPING_MERCHANT_ACTIVE          = 'google/checkout_shipping_merchant/active';
    const XML_PATH_SHIPPING_MERCHANT_ALLOWED_METHODS = 'google/checkout_shipping_merchant/allowed_methods';

    /**
     * Google Checkout Shipping - Pickup settings
     */
    const XML_PATH_SHIPPING_PICKUP_ACTIVE            = 'google/checkout_shipping_pickup/active';
    const XML_PATH_SHIPPING_PICKUP_TITLE             = 'google/checkout_shipping_pickup/title';
    const XML_PATH_SHIPPING_PICKUP_PRICE             = 'google/checkout_shipping_pickup/price';

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Check if option googlecheckout shipping carrier is enabled
     *
     * @param  $storeId
     * @return bool
     */
    public function isShippingCarrierActive($storeId)
    {
        return (true == $this->_coreStoreConfig->getConfig(self::XML_PATH_SHIPPING_CARRIER_ACTIVE, $storeId));
    }

    /**
     * Convert Magento zip range to array of Google Checkout zip-patterns
     * (e.g., 12000-13999 -> [12*, 13*])
     *
     * @param  string $zipRange
     * @return array
     */
    public function zipRangeToZipPattern($zipRange)
    {
        $zipLength = 5;
        $zipPattern = array();

        if (!preg_match("/^(.+)-(.+)$/", $zipRange, $zipParts)) {
            return array($zipRange);
        }

        if ($zipParts[1] == $zipParts[2]) {
            return array($zipParts[1]);
        }

        if ($zipParts[1] > $zipParts[2]) {
            list($zipParts[2], $zipParts[1]) = array($zipParts[1], $zipParts[2]);
        }

        $from = str_split($zipParts[1]);
        $to = str_split($zipParts[2]);

        $startZip = '';
        $diffPosition = null;
        for ($pos = 0; $pos < $zipLength; $pos++) {
            if ($from[$pos] == $to[$pos]) {
                $startZip .= $from[$pos];
            } else {
                $diffPosition = $pos;
                break;
            }
        }

        /*
         * calculate zip-patterns
         */
        if (min(array_slice($to, $diffPosition)) == 9 && max(array_slice($from, $diffPosition)) == 0) {
            // particular case like 11000-11999 -> 11*
            return array($startZip . '*');
        } else {
            // calculate approximate zip-patterns
            $start = $from[$diffPosition];
            $finish = $to[$diffPosition];
            if ($diffPosition < $zipLength - 1) {
                $start++;
                $finish--;
            }
            $end = $diffPosition < $zipLength - 1 ? '*' : '';
            for ($digit = $start; $digit <= $finish; $digit++) {
                $zipPattern[] = $startZip . $digit . $end;
            }
        }

        if ($diffPosition == $zipLength - 1) {
            return $zipPattern;
        }

        $nextAsteriskFrom = true;
        $nextAsteriskTo = true;
        for ($pos = $zipLength - 1; $pos > $diffPosition; $pos--) {
            // calculate zip-patterns based on $from value
            if ($from[$pos] == 0 && $nextAsteriskFrom) {
                $nextAsteriskFrom = true;
            } else {
                $subZip = '';
                for ($k = $diffPosition; $k < $pos; $k++) {
                    $subZip .= $from[$k];
                }
                $delta = $nextAsteriskFrom ? 0 : 1;
                $end = $pos < $zipLength - 1 ? '*' : '';
                for ($i = $from[$pos] + $delta; $i <= 9; $i++) {
                    $zipPattern[] = $startZip . $subZip . $i . $end;
                }
                $nextAsteriskFrom = false;
            }

            // calculate zip-patterns based on $to value
            if ($to[$pos] == 9 && $nextAsteriskTo) {
                $nextAsteriskTo = true;
            } else {
                $subZip = '';
                for ($k = $diffPosition; $k < $pos; $k++) {
                    $subZip .= $to[$k];
                }
                $delta = $nextAsteriskTo ? 0 : 1;
                $end = $pos < $zipLength - 1 ? '*' : '';
                for ($i = 0; $i <= $to[$pos] - $delta; $i++) {
                    $zipPattern[] = $startZip . $subZip . $i . $end;
                }
                $nextAsteriskTo = false;
            }
        }

        if ($nextAsteriskFrom) {
            $zipPattern[] = $startZip . $from[$diffPosition] . '*';
        }
        if ($nextAsteriskTo) {
            $zipPattern[] = $startZip . $to[$diffPosition] . '*';
        }

        return $zipPattern;
    }
}
