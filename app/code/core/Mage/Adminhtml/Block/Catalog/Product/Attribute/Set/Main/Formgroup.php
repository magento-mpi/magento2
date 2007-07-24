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

        $fieldset->addField('new_group', 'text',
                            array(
                                'label' => __('Name'),
                                'name' => 'new_group',
                                'required' => true,
                            )
        );

    	$fieldset->addField('submit', 'note',
                            array(
                                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                                            ->setData(array(
                                                'label'     => __('Add Group'),
                                                'onclick'   => 'this.form.submit();'
                                            ))
                                            ->toHtml(),
                            )
        );

        $form->setUseContainer(true);
        $form->setMethod('POST');
        $this->setForm($form);
    }
}