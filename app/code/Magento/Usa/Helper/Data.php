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
namespace Magento\Usa\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     */
    public function __construct(\Magento\App\Helper\Context $context, \Magento\Core\Model\LocaleInterface $locale)
    {
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
            $unitWeight = new \Zend_Measure_Weight($value, $sourceWeightMeasure, $locale);
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
            $unitDimension = new \Zend_Measure_Length($value, $sourceDimensionMeasure, $locale);
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
        $weight = new \Zend_Measure_Weight(0);
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
        $weight = new \Zend_Measure_Length(0);
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
            'usps_0_FCLE', // First-Class Mail Large Envelope
            'usps_1',      // Priority Mail
            'usps_2',      // Priority Mail Express Hold For Pickup
            'usps_3',      // Priority Mail Express
            'usps_4',      // Standard Post
            'usps_6',      // Media Mail
            'usps_INT_1',  // Priority Mail Express International
            'usps_INT_2',  // Priority Mail International
            'usps_INT_4',  // Global Express Guaranteed (GXG)
            'usps_INT_7',  // Global Express Guaranteed Non-Document Non-Rectangular
            'usps_INT_8',  // Priority Mail International Flat Rate Envelope
            'usps_INT_9',  // Priority Mail International Medium Flat Rate Box
            'usps_INT_10', // Priority Mail Express International Flat Rate Envelope
            'usps_INT_11', // Priority Mail International Large Flat Rate Box
            'usps_INT_12', // USPS GXG Envelopes
            'usps_INT_14', // First-Class Mail International Large Envelope
            'usps_INT_16', // Priority Mail International Small Flat Rate Box
            'usps_INT_20', // Priority Mail International Small Flat Rate Envelope
            'usps_INT_26', // Priority Mail Express International Flat Rate Boxes
        ))) {
            return true;
        } else {
            return false;
        }
    }
}
