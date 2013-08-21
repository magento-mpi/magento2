<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Advanced search form
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Block_Advanced_Form extends Magento_Core_Block_Template
{
    public function _prepareLayout()
    {
        // add Home breadcrumb
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label'=>__('Home'),
                'title'=>__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ))->addCrumb('search', array(
                'label'=>__('Catalog Advanced Search')
            ));
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve collection of product searchable attributes
     *
     * @return Magento_Data_Collection_Db
     */
    public function getSearchableAttributes()
    {
        $attributes = $this->getModel()->getAttributes();
        return $attributes;
    }

    /**
     * Retrieve attribute label
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeLabel($attribute)
    {
        return $attribute->getStoreLabel();
    }

    /**
     * Retrieve attribute input validation class
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeValidationClass($attribute)
    {
        return $attribute->getFrontendClass();
    }

    /**
     * Retrieve search string for given field from request
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string|null $part
     * @return mixed|string
     */
    public function getAttributeValue($attribute, $part = null)
    {
        $value = $this->getRequest()->getQuery($attribute->getAttributeCode());
        if ($part && $value) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                $value = '';
            }
        }

        return $value;
    }

    /**
     * Retrieve the list of available currencies
     *
     * @return array
     */
    public function getAvailableCurrencies()
    {
        $currencies = $this->getData('_currencies');
        if (is_null($currencies)) {
            $currencies = array();
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes) && count($codes)) {
                $rates = Mage::getModel('Magento_Directory_Model_Currency')->getCurrencyRates(
                    Mage::app()->getStore()->getBaseCurrency(),
                    $codes
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $currencies[$code] = $code;
                    }
                }
            }

            $this->setData('currencies', $currencies);
        }
        return $currencies;
    }

    /**
     * Count available currencies
     *
     * @return int
     */
    public function getCurrencyCount()
    {
        return count($this->getAvailableCurrencies());
    }

    /**
     * Retrieve currency code for attribute
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getCurrency($attribute)
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();

        $baseCurrency = Mage::app()->getStore()->getBaseCurrency()->getCurrencyCode();
        return $this->getAttributeValue($attribute, 'currency') ?
            $this->getAttributeValue($attribute, 'currency') : $baseCurrency;
    }

    /**
     * Retrieve attribute input type
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return  string
     */
    public function getAttributeInputType($attribute)
    {
        $dataType   = $attribute->getBackend()->getType();
        $imputType  = $attribute->getFrontend()->getInputType();
        if ($imputType == 'select' || $imputType == 'multiselect') {
            return 'select';
        }

        if ($imputType == 'boolean') {
            return 'yesno';
        }

        if ($imputType == 'price') {
            return 'price';
        }

        if ($dataType == 'int' || $dataType == 'decimal') {
            return 'number';
        }

        if ($dataType == 'datetime') {
            return 'date';
        }

        return 'string';
    }

    /**
     * Build attribute select element html string
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeSelectElement($attribute)
    {
        $extra = '';
        $options = $attribute->getSource()->getAllOptions(false);

        $name = $attribute->getAttributeCode();

        // 2 - avoid yes/no selects to be multiselects
        if (is_array($options) && count($options)>2) {
            $extra = 'multiple="multiple" size="4"';
            $name.= '[]';
        }
        else {
            array_unshift($options, array('value'=>'', 'label'=>__('All')));
        }

        return $this->_getSelectBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attribute))
            ->setExtraParams($extra)
            ->setValue($this->getAttributeValue($attribute))
            ->setOptions($options)
            ->setClass('multiselect')
            ->getHtml();
    }

    /**
     * Retrieve yes/no element html for provided attribute
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeYesNoElement($attribute)
    {
        $options = array(
            array('value' => '',  'label' => __('All')),
            array('value' => '1', 'label' => __('Yes')),
            array('value' => '0', 'label' => __('No'))
        );

        $name = $attribute->getAttributeCode();
        return $this->_getSelectBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attribute))
            ->setExtraParams("")
            ->setValue($this->getAttributeValue($attribute))
            ->setOptions($options)
            ->getHtml();
    }

    protected function _getSelectBlock()
    {
        $block = $this->getData('_select_block');
        if (is_null($block)) {
            $block = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select');
            $this->setData('_select_block', $block);
        }
        return $block;
    }

    protected function _getDateBlock()
    {
        $block = $this->getData('_date_block');
        if (is_null($block)) {
            $block = $this->getLayout()->createBlock('Magento_Core_Block_Html_Date');
            $this->setData('_date_block', $block);
        }
        return $block;
    }

    /**
     * Retrieve advanced search model object
     *
     * @return Magento_CatalogSearch_Model_Advanced
     */
    public function getModel()
    {
        return Mage::getSingleton('Magento_CatalogSearch_Model_Advanced');
    }

    /**
     * Retrieve search form action url
     *
     * @return string
     */
    public function getSearchPostUrl()
    {
        return $this->getUrl('*/*/result');
    }

    /**
     * Build date element html string for attribute
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string $part
     * @return string
     */
    public function getDateInput($attribute, $part = 'from')
    {
        $name = $attribute->getAttributeCode() . '[' . $part . ']';
        $value = $this->getAttributeValue($attribute, $part);

        return $this->_getDateBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode() . ($part == 'from' ? '' : '_' . $part))
            ->setTitle($this->getAttributeLabel($attribute))
            ->setValue($value)
            ->setImage($this->getViewFileUrl('Magento_Core::calendar.gif'))
            ->setDateFormat(Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT))
            ->setClass('input-text')
            ->getHtml();
    }
}
