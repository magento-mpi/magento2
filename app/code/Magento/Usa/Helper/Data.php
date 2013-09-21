<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Usa data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Usa_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Helper_Context $context
    ) {
        $this->_locale = $locale;
        parent::__construct($context);
    }

    /**
     * Convert weight in different measure types
     *
     * @param  mixed $value
     * @param  string $sourceWeightMeasure
     * @param  string $toWeightMeasure
     * @return int|null|string
     */
    public function convertMeasureWeight($value, $sourceWeightMeasure, $toWeightMeasure)
    {
        if ($value) {
            $locale = $this->_locale->getLocale();
            $unitWeight = new Zend_Measure_Weight($value, $sourceWeightMeasure, $locale);
            $unitWeight->setType($toWeightMeasure);
            return $unitWeight->getValue();
        }
        return null;
    }

    /**
     * Convert dimensions in different measure types
     *
     * @param  mixed $value
     * @param  string $sourceDimensionMeasure
     * @param  string $toDimensionMeasure
     * @return int|null|string
     */
    public function convertMeasureDimension($value, $sourceDimensionMeasure, $toDimensionMeasure)
    {
        if ($value) {
            $locale = $this->_locale->getLocale();
            $unitDimension = new Zend_Measure_Length($value, $sourceDimensionMeasure, $locale);
            $unitDimension->setType($toDimensionMeasure);
            return $unitDimension->getValue();
        }
        return null;
    }

    /**
     * Get name of measure by its type
     *
     * @param  $key
     * @return string
     */
    public function getMeasureWeightName($key)
    {
        $weight = new Zend_Measure_Weight(0);
        $conversionList = $weight->getConversionList();
        if (!empty($conversionList[$key]) && !empty($conversionList[$key][1])) {
            return $conversionList[$key][1];
        }
        return '';
    }

    /**
     * Get name of measure by its type
     *
     * @param  $key
     * @return string
     */
    public function getMeasureDimensionName($key)
    {
        $weight = new Zend_Measure_Length(0);
        $conversionList = $weight->getConversionList();
        if (!empty($conversionList[$key]) && !empty($conversionList[$key][1])) {
            return $conversionList[$key][1];
        }
        return '';
    }

    /**
     * Define if we need girth parameter in the package window
     *
     * @param string $shippingMethod
     * @return bool
     */
    public function displayGirthValue($shippingMethod)
    {
        if (in_array($shippingMethod, array(
            'usps_Priority Mail International',
            'usps_Priority Mail International Small Flat Rate Box',
            'usps_Priority Mail International Medium Flat Rate Box',
            'usps_Priority Mail International Large Flat Rate Box',
            'usps_Priority Mail International Flat Rate Envelope',
            'usps_Express Mail International Flat Rate Envelope',
            'usps_Express Mail Hold For Pickup',
            'usps_Express Mail International',
            'usps_First-Class Mail International Package',
            'usps_First-Class Mail International Parcel',
            'usps_First-Class Mail International Large Envelope',
            'usps_First-Class Mail International',
            'usps_Global Express Guaranteed (GXG)',
            'usps_USPS GXG Envelopes',
            'usps_Global Express Guaranteed Non-Document Non-Rectangular',
            'usps_Media Mail',
            'usps_Parcel Post',
            'usps_Express Mail',
            'usps_Priority Mail'
        ))) {
            return true;
        } else {
            return false;
        }
    }
}
