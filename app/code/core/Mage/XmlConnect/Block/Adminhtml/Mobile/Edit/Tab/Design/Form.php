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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Form extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
{
    /**
     * Prepare form
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('fieldLogo', array('legend' => $this->__('Images')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'conf[native][navigationBar][icon]', 'Logo in header');
        $this->addImage($fieldset, 'conf[native][body][bannerImage]', 'Banner on Home Screen');
        $this->addImage($fieldset, 'conf[native][body][backgroundImage]', 'Application Background');
        $this->addImage($fieldset, 'conf[native][body][itemBackgroundIcon]', 'Category Background');

        $fieldset = $form->addFieldset('fieldColors', array('legend' => $this->__('Color Themes')));
        $this->_addElementTypes($fieldset);
        $fieldset->addField('conf[extra][theme]', 'theme', array(
            'name'      => 'conf[extra][theme]',
            'themes'    => Mage::helper('xmlconnect/data')->getThemes(),
        ));
        $this->addColor($fieldset, 'conf[native][navigationBar][tintColor]', $this->__('Header Background Color'));
        $this->addImage($fieldset, 'conf[native][body][rowBackgroundIcon]', $this->__('Primary Background Image'));
        $this->addImage($fieldset, 'conf[native][sortingBar][backgroundImage]', $this->__('Title Background Image'));
        $this->addImage($fieldset, 'conf[native][body][addToCartBackgroundIcon]', $this->__('Button Background Image'));
        $this->addImage($fieldset, 'conf[native][itemActions][backgroundImage]', $this->__('Context Menu Background Image'));

        $fieldset = $form->addFieldset('fieldFonts', array('legend' => $this->__('Fonts')));
        $this->_addElementTypes($fieldset);
        $this->addFont($fieldset, 'conf[native][fonts][Title1]', $this->__('Navigation bar title'));
        $this->addFont($fieldset, 'conf[native][fonts][Title2]', $this->__('Main header font'));
        $this->addFont($fieldset, 'conf[native][fonts][Title3]', $this->__('Applied filters values'));
        $this->addFont($fieldset, 'conf[native][fonts][Title4]', $this->__('Applied filters title'));
        $this->addFont($fieldset, 'conf[native][fonts][Title5]', $this->__('Price'));
        $this->addFont($fieldset, 'conf[native][fonts][Title6]', $this->__('Add to cart button label'));
        $this->addFont($fieldset, 'conf[native][fonts][Title7]', $this->__('Related products label'));
        $this->addFont($fieldset, 'conf[native][fonts][Title8]', $this->__('Item actions font'));
        $this->addFont($fieldset, 'conf[native][fonts][Text1]', $this->__('Version number'));
        $this->addFont($fieldset, 'conf[native][fonts][Text2]', $this->__('Main description text'));

        $fieldset = $form->addFieldset('fieldTabs', array('legend' => $this->__('Tabs')));
        $this->_addElementTypes($fieldset);
        $fieldset->addField('conf[extra][tabs]', 'tabs', array('name' => 'conf[extra][tabs]'));

        $model = Mage::registry('current_app');
        $form->setValues($model->getFormData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
