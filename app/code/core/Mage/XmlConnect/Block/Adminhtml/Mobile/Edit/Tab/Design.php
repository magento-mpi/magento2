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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }


    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('navigationBar', array('legend' => $this->__('Navigation Bar')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField('conf/navigationBar/tintColor', 'text', array(
            'name'      => 'conf/navigationBar/tintColor',
            'label'     => $this->__('Tint Color'),
        ));

        $fieldset->addField('conf/navigationBar/backgroundColor', 'text', array(
            'name'      => 'conf/navigationBar/backgroundColor',
            'label'     => $this->__('Background Color'),
        ));

        $fieldset->addField('conf/navigationBar/icon', 'image', array(
            'name'      => 'conf/navigationBar/icon',
            'label'     => $this->__('Icon'),
        ));

        $fieldset->addField('conf/navigationBar/font/name', 'select', array(
            'name'      => 'conf/navigationBar/font/name',
            'label'     => $this->__('Font Name'),
            'values'    => Mage::helper('xmlconnect')->getFontList(),
        ));

        $fieldset->addField('conf/navigationBar/font/size', 'text', array(
            'name'      => 'conf/navigationBar/font/size',
            'label'     => $this->__('Font Size'),
        ));

        $fieldset->addField('conf/navigationBar/font/color', 'text', array(
            'name'      => 'conf/navigationBar/font/color',
            'label'     => $this->__('Font Color'),
        ));

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('tabBar', array('legend' => $this->__('Tab Bar')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField('conf/tabBar/backgroundColor', 'text', array(
            'name'      => 'conf/tabBar/backgroundColor',
            'label'     => $this->__('Background Color'),
        ));

        $fieldset->addField('conf/tabBar/font/name', 'select', array(
            'name'      => 'conf/tabBar/font/name',
            'label'     => $this->__('Font Name'),
            'values'    => Mage::helper('xmlconnect')->getFontList(),
        ));

        $fieldset->addField('conf/tabBar/home/icon', 'image', array(
            'name'      => 'conf/tabBar/home/icon',
            'label'     => $this->__('Home Tab Icon'),
        ));

        $fieldset->addField('conf/tabBar/home/title', 'text', array(
            'name'      => 'conf/tabBar/home/title',
            'label'     => $this->__('Home Tab Title'),
        ));

        $fieldset->addField('conf/tabBar/shop/icon', 'image', array(
            'name'      => 'conf/tabBar/shop/icon',
            'label'     => $this->__('Shop Tab Icon'),
        ));

        $fieldset->addField('conf/tabBar/shop/title', 'text', array(
            'name'      => 'conf/tabBar/shop/title',
            'label'     => $this->__('Shop Tab Title'),
        ));

        $fieldset->addField('conf/tabBar/cart/icon', 'image', array(
            'name'      => 'conf/tabBar/cart/icon',
            'label'     => $this->__('Cart Tab Icon'),
        ));

        $fieldset->addField('conf/tabBar/cart/title', 'text', array(
            'name'      => 'conf/tabBar/cart/title',
            'label'     => $this->__('Cart Tab Title'),
        ));

        $fieldset->addField('conf/tabBar/search/icon', 'image', array(
            'name'      => 'conf/tabBar/search/icon',
            'label'     => $this->__('Search Tab Icon'),
        ));

        $fieldset->addField('conf/tabBar/search/title', 'text', array(
            'name'      => 'conf/tabBar/search/title',
            'label'     => $this->__('Search Tab Title'),
        ));

        $fieldset->addField('conf/tabBar/more/icon', 'image', array(
            'name'      => 'conf/tabBar/more/icon',
            'label'     => $this->__('More Tab Icon'),
        ));

        $fieldset->addField('conf/tabBar/more/title', 'text', array(
            'name'      => 'conf/tabBar/more/title',
            'label'     => $this->__('More Tab Title'),
        ));

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('bodyPart', array('legend' => $this->__('Body')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField('conf/body/backgroundColor', 'text', array(
            'name'      => 'conf/body/backgroundColor',
            'label'     => $this->__('Background Color'),
        ));

        $fieldset->addField('conf/body/scrollBackgroundColor', 'text', array(
            'name'      => 'conf/body/scrollBackgroundColor',
            'label'     => $this->__('Scroll Background Color'),
        ));

        $fieldset->addField('conf/body/itemBackgroundIcon', 'image', array(
            'name'      => 'conf/body/itemBackgroundIcon',
            'label'     => $this->__('Item Background'),
        ));

        $model = Mage::registry('current_app');
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Application Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Application Design');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_image'),
        );
    }
}
