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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom Variable Edit Form
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Variable_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Mage_Core_Model_Variable
     */
    public function getVariable()
    {
        return Mage::registry('current_variable');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_System_Variable_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));
        $fieldset = $form->addFieldset('base', array());
        $fieldset->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => Mage::helper('adminhtml')->__('Variable Code'),
            'title'    => Mage::helper('adminhtml')->__('Variable Code'),
            'required' => true
        ));
        $fieldset->addField('name', 'text', array(
            'name'     => 'name',
            'label'    => Mage::helper('adminhtml')->__('Variable Name'),
            'title'    => Mage::helper('adminhtml')->__('Variable Name'),
            'required' => true
        ));
        $fieldset->addField('is_html', 'select', array(
            'name'   => 'is_html',
            'label'  => Mage::helper('adminhtml')->__('Show Content as'),
            'title'  => Mage::helper('adminhtml')->__('Show Content as'),
            'values' => array(
                0 => Mage::helper('adminhtml')->__('Text'),
                1 => Mage::helper('adminhtml')->__('HTML')
        )));
        $fieldset->addField('value', 'textarea', array(
            'name'     => 'value',
            'label'    => Mage::helper('adminhtml')->__('Variable Value'),
            'title'    => Mage::helper('adminhtml')->__('Variable Value'),
            'required' => true
        ))->setRenderer(
            $this->getLayout()
                ->createBlock('adminhtml/system_variable_form_renderer_fieldset_element')
                ->setVariable($this->getVariable())
        );
        $form->setValues($this->getVariable()->getData())
            ->addFieldNameSuffix('variable')
            ->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
