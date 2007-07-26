<?php
/**
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formset extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', array('legend'=>__('Add New Set')));

        $fieldset->addField('new_set', 'text',
                            array(
                                'label' => __('Name'),
                                'name' => 'new_set',
                                'required' => true,
                            )
        );

        $collection = Mage::getModel('eav/entity_attribute_set')
            ->getResourceCollection()
            ->load()
            ->toOptionArray();

    	$fieldset->addField('set_base', 'select',
                            array(
                                'label' => __('Based on'),
                                'name' => 'set_switcher',
                                'title' => __('Please Choose Set'),
                                'values' => $collection,
                            )
        );

    	$fieldset->addField('submit', 'note',
                            array(
                                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                                            ->setData(array(
                                                'label'     => __('Add Set'),
                                                'onclick'   => 'this.form.submit();',
																								'class' => 'add'
                                            ))
                                            ->toHtml(),
                            )
        );

        $form->setUseContainer(true);
        $form->setMethod('POST');
        $this->setForm($form);
    }
}