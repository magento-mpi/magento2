<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin product tax class add form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Tax_Rate_Form extends Magento_Backend_Block_Widget_Form
{
    const FORM_ELEMENT_ID = 'rate-form';

    protected $_titles = null;

    protected $_template = 'tax/rate/form.phtml';


    protected function _construct()
    {
        parent::_construct();
        $this->setDestElementId(self::FORM_ELEMENT_ID);

    }

    protected function _prepareForm()
    {
        $rateObject = new \Magento\Object(Mage::getSingleton('Magento_Tax_Model_Calculation_Rate')->getData());
        $form = new \Magento\Data\Form();

        $countries = Mage::getModel('Magento_Directory_Model_Config_Source_Country')->toOptionArray(false, 'US');
        unset($countries[0]);

        if (!$rateObject->hasTaxCountryId()) {
            $rateObject->setTaxCountryId(Mage::getStoreConfig(Magento_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY));
        }

        if (!$rateObject->hasTaxRegionId()) {
            $rateObject->setTaxRegionId(Mage::getStoreConfig(Magento_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION));
        }

        $regionCollection = Mage::getModel('Magento_Directory_Model_Region')
            ->getCollection()
            ->addCountryFilter($rateObject->getTaxCountryId());

        $regions = $regionCollection->toOptionArray();
        if ($regions) {
            $regions[0]['label'] = '*';
        } else {
            $regions = array(array('value' => '', 'label' => '*'));
        }

        $legend = $this->getShowLegend() ? __('Tax Rate Information') : '';
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $legend));

        if ($rateObject->getTaxCalculationRateId() > 0) {
            $fieldset->addField('tax_calculation_rate_id', 'hidden', array(
                'name'  => 'tax_calculation_rate_id',
                'value' => $rateObject->getTaxCalculationRateId()
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => __('Tax Identifier'),
            'title'    => __('Tax Identifier'),
            'class'    => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('zip_is_range', 'checkbox', array(
            'name'    => 'zip_is_range',
            'label'   => __('Zip/Post is Range'),
            'value'   => '1'
        ));

        if (!$rateObject->hasTaxPostcode()) {
            $rateObject->setTaxPostcode(Mage::getStoreConfig(Magento_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_POSTCODE));
        }

        $fieldset->addField('tax_postcode', 'text', array(
            'name'  => 'tax_postcode',
            'label' => __('Zip/Post Code'),
            'note'  => __("'*' - matches any; 'xyz*' - matches any that begins on 'xyz' and are not longer than %1.", Mage::helper('Magento_Tax_Helper_Data')->getPostCodeSubStringLength()),
        ));

        $fieldset->addField('zip_from', 'text', array(
            'name'      => 'zip_from',
            'label'     => __('Range From'),
            'required'  => true,
            'maxlength' => 9,
            'class'     => 'validate-digits',
            'css_class'     => 'hidden',
        ));

        $fieldset->addField('zip_to', 'text', array(
            'name'      => 'zip_to',
            'label'     => __('Range To'),
            'required'  => true,
            'maxlength' => 9,
            'class'     => 'validate-digits',
            'css_class'     => 'hidden',
        ));

        $fieldset->addField('tax_region_id', 'select', array(
            'name'   => 'tax_region_id',
            'label'  => __('State'),
            'values' => $regions
        ));

        $fieldset->addField('tax_country_id', 'select', array(
            'name'     => 'tax_country_id',
            'label'    => __('Country'),
            'required' => true,
            'values'   => $countries
        ));

        $fieldset->addField('rate', 'text', array(
            'name'     => 'rate',
            'label'    => __('Rate Percent'),
            'title'    => __('Rate Percent'),
            'required' => true,
            'class'    => 'validate-not-negative-number'
        ));

        $form->setAction($this->getUrl('adminhtml/tax_rate/save'));
        $form->setUseContainer(true);
        $form->setId(self::FORM_ELEMENT_ID);
        $form->setMethod('post');

        if (!Mage::app()->hasSingleStore()) {
            $form->addElement(
                Mage::getBlockSingleton('Magento_Adminhtml_Block_Tax_Rate_Title_Fieldset')
                    ->setLegend(__('Tax Titles'))
            );
        }

        $rateData = $rateObject->getData();
        if ($rateObject->getZipIsRange()) {
            list($rateData['zip_from'], $rateData['zip_to']) = explode('-', $rateData['tax_postcode']);
        }
        $form->setValues($rateData);
        $this->setForm($form);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento_Core_Block_Template')
                ->setTemplate('Magento_Adminhtml::tax/rate/js.phtml')
        );

        return parent::_prepareForm();
    }

    /**
     * Get Tax Rates Collection
     *
     * @return array
     */
    public function getRateCollection()
    {
        if ($this->getData('rate_collection') == null) {
            $rateCollection = Mage::getModel('Magento_Tax_Model_Calculation_Rate')->getCollection()
                ->joinRegionTable();
            $rates = array();

            foreach ($rateCollection as $rate) {
                $item = $rate->getData();
                foreach ($rate->getTitles() as $title) {
                    $item['title[' . $title->getStoreId() . ']'] = $title->getValue();
                }
                $rates[] = $item;
            }

            $this->setRateCollection($rates);
        }
        return $this->getData('rate_collection');
    }
}
