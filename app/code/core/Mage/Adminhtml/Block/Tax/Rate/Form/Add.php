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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin product tax class add form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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

        if( isset($regions) && count($regions) > 0 ) {
            $regions[0]['value'] = '';
        }

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
                                'title' => __('Please select State'),
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
                                'title' => __('Please select County'),
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

        $fieldset->addField('tax_postcode', 'text',
                            array(
                                'name' => 'tax_postcode',
                                'label' => __('Zip/Post Code'),
                                'value' => $rateObject->getTaxPostcode()
                            )
        );

        $rateTypeCollection = Mage::getResourceModel('tax/rate_type_collection')->load();

        foreach( $rateTypeCollection as $rateType ) {
            $fieldset->addField('rate_data_'.$rateType->getTypeId(), 'text',
                                array(
                                    'name' => "rate_data[{$rateType->getTypeId()}]",
                                    'label' => $rateType->getTypeName(),
                                    'title' => $rateType->getTypeName(),
                                    'value' => $rateObject->getData("rate_value{$rateType->getTypeId()}"),
                                    'class' => 'validate-not-negative-number'
                                )
            );
        }

        $form->setAction(Mage::getUrl('*/tax_rate/save'));
        $form->setUseContainer(true);
        $form->setId('rate_form');
        $form->setMethod('POST');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
