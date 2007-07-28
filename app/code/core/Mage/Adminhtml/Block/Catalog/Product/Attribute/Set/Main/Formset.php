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
        $data = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('set_name', array('legend'=>__('Edit Set Name')));
        $fieldset->addField('attribute_set_name', 'text',
                            array(
                                'label' => __('Name'),
                                'name' => 'attribute_set_name',
                                'required' => true,
                                'class' => 'required-entry',
                                'value' => $data->getAttributeSetName()
                            )
        );

        $form->setUseContainer(true);
        $form->setId('set_prop_form');
        $this->setForm($form);
    }
}