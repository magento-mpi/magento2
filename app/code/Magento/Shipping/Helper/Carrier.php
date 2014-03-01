<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Helper;

/**
 * Carrier helper
 */
class Carrier extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Carriers root xml path
     */
    const XML_PATH_CARRIERS_ROOT = 'carriers';

    /**
     * Locale interface
     *
     * @var \Magento\LocaleInterface
     */
    protected $locale;

    /**
     * Store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $storeConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\LocaleInterface $locale
     * @param \Magento\Core\Model\Store\ConfigInterface $storeConfig
    */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\LocaleInterface $locale,
        \Magento\Core\Model\Store\ConfigInterface $storeConfig
    ) {
        $this->locale = $locale;
        $this->storeConfig = $storeConfig;
        parent::__construct($context);
    }

    /**
     * Get online shipping carrier codes
     *
     * @param int|\Magento\Core\Model\Store|null $store
     * @return array
     */
    public function getOnlineCarrierCodes($store = null)
    {
        $carriersCodes = array();
        foreach ($this->storeConfig->getConfig(self::XML_PATH_CARRIERS_ROOT, $store) as $carrierCode => $carrier) {
            if (isset($carrier['is_online']) && $carrier['is_online']) {
                $carriersCodes[] = $carrierCode;
            }
        }
        return $carriersCodes;
    }

    /**
     * Get shipping carrier config value
     *
     * @param string $carrierCode
     * @param string $configPath
     * @param null $store
     * @return string
     */
    public function getCarrierConfigValue($carrierCode, $configPath, $store = null)
    {
        return $this->storeConfig->getConfig(
            sprintf('%s/%s/%s', self::XML_PATH_CARRIERS_ROOT, $carrierCode , $configPath),
            $store
        );
    }

    /**
     * Convert weight in different measure types
     *
     * @param int|float $value
     * @param string $sourceWeightMeasure
     * @param string $toWeightMeasure
     * @return int|null|string
     */
    public function convertMeasureWeight($value, $sourceWeightMeasure, $toWeightMeasure)
    {
        if ($value) {
            $locale = $this->locale->getLocale();
            $unitWeight = new \Zend_Measure_Weight($value, $sourceWeightMeasure, $locale);
            $unitWeight->setType($toWeightMeasure);
            return $unitWeight->getValue();
        }
        return null;
    }

    /**
     * Convert dimensions in different measure types
     *
     * @param  int|float $value
     * @param  string $sourceDimensionMeasure
     * @param  string $toDimensionMeasure
     * @return int|null|string
     */
    public function convertMeasureDimension($value, $sourceDimensionMeasure, $toDimensionMeasure)
    {
        if ($value) {
            $locale = $this->locale->getLocale();
            $unitDimension = new \Zend_Measure_Length($value, $sourceDimensionMeasure, $locale);
            $unitDimension->setType($toDimensionMeasure);
            return $unitDimension->getValue();
        }
        return null;
    }

    /**
     * Get name of measure by its type
     *
     * @param string $key
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
     * @param string $key
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
}
