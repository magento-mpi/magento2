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
 * @category   Mage
 * @package    Mage_Adminhtml
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

        $fieldset = $form->addFieldset('set_fieldset', array('legend'=>Mage::helper('catalog')->__('Add New Group')));

        $fieldset->addField('attribute_group_name', 'text',
                            array(
                                'label' => Mage::helper('catalog')->__('Name'),
                                'name' => 'attribute_group_name',
                                'required' => true,
                            )
        );

    	$fieldset->addField('submit', 'note',
                            array(
                                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                                            ->setData(array(
                                                'label'     => Mage::helper('catalog')->__('Add Group'),
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
        $form->setMethod('post');
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