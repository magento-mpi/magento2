<?php
/**
 * Admin product tax class add form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Rate_Form_Add extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('rate_form');
    }

    protected function _prepareForm()
    {
        $rateId = $this->getRequest()->getParam('rate');
        $rateObject = new Varien_Object();
        $rateObject->setData(Mage::getSingleton('tax/rate')->loadWithAttributes($rateId));

        $form = new Varien_Data_Form();

        $regions = Mage::getResourceModel('directory/region_collection')
            ->addCountryCodeFilter('USA')
            ->load()
            ->toOptionArray();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Tax Rate Information')));

        if( $rateObject->getTaxRateId() > 0 ) {
            $fieldset->addField('tax_rate_id', 'hidden',
                                array(
                                    'name' => "tax_rate_id",
                                    'value' => $rateObject->getTaxRateId()
                                )
            );
        }

        $fieldset->addField('tax_region_id', 'select',
                            array(
                                'name' => 'tax_region_id',
                                'label' => __('State'),
                                'title' => __('Please, select State'),
                                'class' => 'required-entry',
                                'required' => true,
                                'values' => $regions,
                                'value' => $rateObject->getTaxRegionId()
                            )
        );

        /* FIXME!!! {*/
        $fieldset->addField('tax_county_id', 'select',
                            array(
                                'name' => 'tax_county_id',
                                'label' => __('County'),
                                'title' => __('Please, select County'),
                                'values' => array(
                                    array(
                                        'label' => '*',
                                        'value' => ''
                                    )
                                ),
                                'value' => $rateObject->getTaxCountyId()
                            )
        );
        /*} */

        $fieldset->addField('tax_zip_code', 'text',
                            array(
                                'name' => 'tax_zip_code',
                                'label' => __('Zip/Post Code'),
                                'title' => __('Zip/Post Code Title'),
                                'value' => $rateObject->getTaxZipCode()
                            )
        );

        $rateTypeCollection = Mage::getResourceModel('tax/rate_type_collection')->load();

        foreach( $rateTypeCollection as $rateType ) {
            $fieldset->addField('rate_data'.$rateType->getTypeId(), 'text',
                                array(
                                    'name' => "rate_data[{$rateType->getTypeId()}]",
                                    'label' => $rateType->getTypeName(),
                                    'title' => $rateType->getTypeName(),
                                    'value' => $rateObject->getData("rate_value{$rateType->getTypeId()}"),
                                    'class' => 'validate-not-negative-number'
                                )
            );
        }

        $form->setAction(Mage::getUrl('adminhtml/tax_rate/save'));
        $form->setUseContainer(true);
        $form->setId('rate_form');
        $form->setMethod('POST');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
