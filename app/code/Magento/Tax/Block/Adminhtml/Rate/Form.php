<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin product tax class add form
 *
 * @category   Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Tax\Block\Adminhtml\Rate;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    const FORM_ELEMENT_ID = 'rate-form';

    /**
     * @var null
     */
    protected $_titles = null;

    /**
     * @var string
     */
    protected $_template = 'rate/form.phtml';

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data|null
     */
    protected $_taxData = null;

    /**
     * @var \Magento\Tax\Block\Adminhtml\Rate\Title\FieldsetFactory
     */
    protected $_fieldsetFactory;

    /**
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    protected $_rateFactory;

    /**
     * @var \Magento\Tax\Model\Calculation\Rate
     */
    protected $_rate;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_country;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param \Magento\Tax\Block\Adminhtml\Rate\Title\FieldsetFactory $fieldsetFactory
     * @param \Magento\Tax\Model\Calculation\RateFactory $rateFactory
     * @param \Magento\Tax\Model\Calculation\Rate $rate
     * @param \Magento\Tax\Helper\Data $taxData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Tax\Block\Adminhtml\Rate\Title\FieldsetFactory $fieldsetFactory,
        \Magento\Tax\Model\Calculation\RateFactory $rateFactory,
        \Magento\Tax\Model\Calculation\Rate $rate,
        \Magento\Tax\Helper\Data $taxData,
        array $data = array()
    ) {
        $this->_regionFactory = $regionFactory;
        $this->_country = $country;
        $this->_fieldsetFactory = $fieldsetFactory;
        $this->_rateFactory = $rateFactory;
        $this->_rate = $rate;
        $this->_taxData = $taxData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDestElementId(self::FORM_ELEMENT_ID);

    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $rateObject = new \Magento\Object($this->_rate->getData());
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $countries = $this->_country->toOptionArray(false, 'US');
        unset($countries[0]);

        if (!$rateObject->hasTaxCountryId()) {
            $rateObject->setTaxCountryId($this->_storeConfig->getConfig(
                \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_COUNTRY
            ));
        }

        if (!$rateObject->hasTaxRegionId()) {
            $rateObject->setTaxRegionId($this->_storeConfig->getConfig(
                \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_REGION
            ));
        }

        $regionCollection = $this->_regionFactory->create()->getCollection()
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
            $rateObject->setTaxPostcode($this->_storeConfig->getConfig(
                \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_POSTCODE
            ));
        }

        $fieldset->addField('tax_postcode', 'text', array(
            'name'  => 'tax_postcode',
            'label' => __('Zip/Post Code'),
            'note'  => __("'*' - matches any; 'xyz*' - matches any that begins on 'xyz' and are not longer than %1.",
                $this->_taxData->getPostCodeSubStringLength()),
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

        $form->setAction($this->getUrl('tax/rate/save'));
        $form->setUseContainer(true);
        $form->setId(self::FORM_ELEMENT_ID);
        $form->setMethod('post');

        if (!$this->_storeManager->hasSingleStore()) {
            $form->addElement(
                $this->_fieldsetFactory->create()->setLegend(__('Tax Titles'))
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
            $this->getLayout()->createBlock('Magento\View\Element\Template')
                ->setTemplate('Magento_Tax::rate/js.phtml')
        );

        return parent::_prepareForm();
    }

    /**
     * Get Tax Rates Collection
     *
     * @return mixed
     */
    public function getRateCollection()
    {
        if ($this->getData('rate_collection') == null) {
            $rateCollection = $this->_rateFactory->create()->getCollection()
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
