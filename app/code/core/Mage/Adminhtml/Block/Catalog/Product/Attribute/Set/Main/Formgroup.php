<?php
/**
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formgroup extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', array('legend'=>__('Add New Group')));

        $fieldset->addField('attribute_group_name', 'text',
                            array(
                                'label' => __('Name'),
                                'name' => 'attribute_group_name',
                                'required' => true,
                            )
        );

    	$fieldset->addField('submit', 'note',
                            array(
                                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                                            ->setData(array(
                                                'label'     => __('Add Group'),
                                                'onclick'   => 'this.form.submit();',
																								'class' => 'add'
                                            ))
                                            ->toHtml(),
                            )
        );

        $fieldset->addField('attribute_set_id', 'hidden',
                            array(
                                'name' => 'attribute_set_id',
                                'value' => $this->_getSetId(),
                            )

        );

        $form->setUseContainer(true);
        $form->setMethod('POST');
        $form->setAction(Mage::getUrl('*/catalog_product_group/save'));
        $this->setForm($form);
    }

    protected function _getSetId()
    {
        return ( intval($this->getRequest()->getParam('id')) > 0 )
                    ? intval($this->getRequest()->getParam('id'))
                    : Mage::getModel('eav/entity_type')
                        ->load(Mage::registry('entityType'))
                        ->getDefaultAttributeSetId();
    }
}