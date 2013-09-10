<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry advanced search block
 */
class Magento_GiftRegistry_Block_Search_Advanced extends Magento_GiftRegistry_Block_Form_Element
{
    protected $_attributes = null;
    protected $_formData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $configCacheType, $data);
    }

    /**
     * Get config
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->_storeConfig->getConfig($path);
    }

    /**
     * Block constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setFieldIdPrefix('params_');
        $this->setFieldNameContainer('params');
    }

    /**
     * Retrieve by key saved in session form data
     *
     * @param string $key
     * @return mixed
     */
    public function getFormData($key)
    {
        if (is_null($this->_formData)) {
            $this->_formData = Mage::getSingleton('Magento_Customer_Model_Session')->getRegistrySearchData();
        }
        if (!$this->_formData || !isset($this->_formData[$key])) {
            return null;
        }
        return $this->escapeHtml($this->_formData[$key]);
    }

    /**
     * Prepare array of searcheable attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $type = $this->_coreRegistry->registry('current_giftregistry_type');
            $config = Mage::getSingleton('Magento_GiftRegistry_Model_Attribute_Config');
            $staticTypes = $config->getStaticTypesCodes();

            $attributes = array();
            foreach ($type->getAttributes() as $group) {
                $attributes = array_merge($attributes, $group);
            }

            $isDate = false;
            $isCountry = false;

            foreach ($attributes as $code => $attribute) {
                if (!in_array($code, $staticTypes) || empty($attribute['frontend']['is_searcheable'])) {
                    unset($attributes[$code]);
                    continue;
                }
                switch ($attribute['type']) {
                    case 'date' : $isDate = $code; break;
                    case 'country' : $isCountry = $code; break;
                }
            }

            /*
             * Create date range html elements instead of date select element
             */
            if ($isDate) {
                $fromDate = $isDate . '_from';
                $attributes[$fromDate] = $attributes[$isDate];
                $attributes[$fromDate]['label'] .= ' ' . __('From');

                $toDate = $isDate . '_to';
                $attributes[$toDate] = $attributes[$isDate];
                $attributes[$toDate]['label'] .= ' ' . __('To');

                unset($attributes[$isDate]);
            }

            /*
             * Add region updater js object to form
             */
            if ($isCountry && !empty($attributes[$isCountry]['show_region'])) {
                $region = $config->getStaticRegionType();
                $this->setRegionJsVisible(true)
                    ->setElementCountry($isCountry)
                    ->setElementRegion($region)
                    ->setElementRegionText($region . '_text');

                $regionAttribute['label'] = __('State/Province');
                $regionAttribute['code'] = $region;
                $regionAttribute['type'] = 'region';

                $formValue = $this->getFormData($isCountry);
                if ($formValue) {
                    $regionAttribute['country'] = $formValue;
                }
                $attributes[$region] = $regionAttribute;
            }

            $this->_attributes = $attributes;
        }
        return $this->_attributes;
    }

    /**
     * Render gift registry attribute as html element
     * @param string $code
     * @return string
     */
    public function renderField($code)
    {
        $attributes = $this->getAttributes();
        $element = '';
        $value = $this->getFormData($code);

        if (empty($attributes[$code])) {
            return $element;
        } else {
            $attribute = $attributes[$code];
        }

        switch ($attribute['type']) {
            case 'text' :
                $element = $this->getInputTextHtml($code, $code, $value);
                break;
            case 'select' :
                $options = $this->convertArrayToOptions($attribute['options'], true);
                $element = $this->getSelectHtml($code, $code, $options, $value);
                break;
            case 'date' :
                $element = $this->getCalendarDateHtml($code, $code, $value, $attribute['date_format']);
                break;
            case 'region' :
                $regionCountry = (isset($attribute['country'])) ? $attribute['country'] : null;
                $element = $this->getRegionHtmlSelect($code, $code, $value, $regionCountry);
                if ($this->getRegionJsVisible()) {
                    $code = $this->getElementRegionText();
                    $value = $this->getFormData($code);
                    $element .= $this->getInputTextHtml($code, $code, $value, '', 'display:none');
                }
                break;
            case 'country' :
                $element = $this->getCountryHtmlSelect($code, $code, $value);
                break;
        }
        return $element;
    }
}
