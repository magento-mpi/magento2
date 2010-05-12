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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * If title is empty, construct it from field name ('backgroundImage' => 'Background Image')
     *
     * @param string $title
     * @param string $field
     * @return string
     */
    protected function getDefaultTitle($title, $field)
    {
        if (!is_null($title)) {
            return $title;
        }
        $field = basename($field);
        $field = preg_replace('/([a-z])([A-Z])/', '$1 $2', $field);
        return ucwords($field);
    }

    /**
     * Add color chooser to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addColor($fieldset, $fieldName, $title=NULL)
    {
        $title = $this->getDefaultTitle($title, $fieldName);
        $fieldName = 'conf/'.$fieldName;
        $fieldset->addField($fieldName, 'text', array(
            'name'      => $fieldName,
            'label'     => $this->__($title),
        ));
    }

    /**
     * Add image uploader to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addImage($fieldset, $fieldName, $title=NULL)
    {
        $title = $this->getDefaultTitle($title, $fieldName);
        $fieldName = 'conf/'.$fieldName;
        $fieldset->addField($fieldName, 'image', array(
            'name'      => $fieldName,
            'label'     => $this->__($title),
        ));
    }

    /**
     * Add font selector to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldPrefix
     * @param string $title
     */
    protected function addFont($fieldset, $fieldPrefix, $title=NULL)
    {
        $title = $this->getDefaultTitle($title, $fieldPrefix);
        $fieldPrefix = 'conf/'.$fieldPrefix;
        $fieldset->addField($fieldPrefix.'/name', 'select', array(
            'name'      => $fieldPrefix.'/name',
            'label'     => $this->__($title.' Name'),
            'values'    => Mage::helper('xmlconnect')->getFontList(),
        ));
        $fieldset->addField($fieldPrefix.'/size', 'text', array(
            'name'      => $fieldPrefix.'/size',
            'label'     => $this->__($title.' Size'),
        ));
        $fieldset->addField($fieldPrefix.'/color', 'text', array(
            'name'      => $fieldPrefix.'/color',
            'label'     => $this->__($title.' Color'),
        ));
    }

    /**
     * Add tab inputs to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldPrefix
     * @param string $title
     */
    protected function addTab($fieldset, $fieldPrefix, $title=NULL)
    {
        $title = $this->getDefaultTitle($title, $fieldPrefix);
        $fieldPrefix = 'conf/'.$fieldPrefix;
        $fieldset->addField($fieldPrefix.'/icon', 'image', array(
            'name'      => $fieldPrefix.'/icon',
            'label'     => $this->__($title.' Tab Icon'),
        ));
        $fieldset->addField($fieldPrefix.'/title', 'text', array(
            'name'      => $fieldPrefix.'/title',
            'label'     => $this->__($title.' Tab Title'),
        ));
    }

    /**
     * Prepare form
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('navigationBar', array('legend' => $this->__('Navigation Bar')));
        $this->_addElementTypes($fieldset);
        $this->addColor($fieldset, 'navigationBar/tintColor');
        $this->addColor($fieldset, 'navigationBar/backgroundColor');
        $this->addImage($fieldset, 'navigationBar/icon');
        $this->addFont($fieldset, 'navigationBar/font');

        $fieldset = $form->addFieldset('sortingBar', array('legend' => $this->__('Sorting Bar')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'sortingBar/backgroundImage');
        $this->addColor($fieldset, 'sortingBar/tintColor');
        $this->addFont($fieldset, 'sortingBar/font');

        $fieldset = $form->addFieldset('tabBar', array('legend' => $this->__('Tab Bar')));
        $this->_addElementTypes($fieldset);
        $this->addTab($fieldset, 'tabBar/home');
        $this->addTab($fieldset, 'tabBar/shop');
        $this->addTab($fieldset, 'tabBar/cart');
        $this->addTab($fieldset, 'tabBar/search');
        $this->addTab($fieldset, 'tabBar/more');

        $fieldset = $form->addFieldset('bodyPart', array('legend' => $this->__('Body')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'body/bannerImage');
        $this->addColor($fieldset, 'body/backgroundColor');
        $this->addImage($fieldset, 'body/backgroundImage');
        $this->addColor($fieldset, 'body/scrollBackgroundColor');
        $this->addImage($fieldset, 'body/itemBackgroundIcon');
        $this->addImage($fieldset, 'body/rowBackgroundIcon');
        $this->addImage($fieldset, 'body/rowAttributeIcon');
        $this->addImage($fieldset, 'body/addToCartBackgroundIcon');
        $this->addImage($fieldset, 'body/actionsBackgroundIcon');
        $this->addImage($fieldset, 'body/reviewsBackgroundIcon');
        $this->addFont($fieldset, 'body/categoryItemFont');
        $this->addFont($fieldset, 'body/copyrightFont');
        $this->addFont($fieldset, 'body/versionFont');
        $this->addFont($fieldset, 'body/productButtonFont');
        $this->addFont($fieldset, 'body/nameFont');
        $this->addFont($fieldset, 'body/priceFont');
        $this->addFont($fieldset, 'body/plainFont');
        $this->addFont($fieldset, 'body/textFont');
        $this->addFont($fieldset, 'body/ratingHeaderFont');

        $fieldset = $form->addFieldset('filters', array('legend' => $this->__('Filters')));
        $this->_addElementTypes($fieldset);
        $this->addFont($fieldset, 'filters/nameFont');
        $this->addFont($fieldset, 'filters/valueFont');

        $fieldset = $form->addFieldset('appliedFilters', array('legend' => $this->__('Applied Filters')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'appliedFilters/backgroundImage');
        $this->addColor($fieldset, 'appliedFilters/backgroundColor');
        $this->addFont($fieldset, 'appliedFilters/font');
        $this->addFont($fieldset, 'appliedFilters/counfFont');
        $this->addFont($fieldset, 'appliedFilters/titleFont');

        $fieldset = $form->addFieldset('itemActions', array('legend' => $this->__('Item Actions')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'itemActions/backgroundImage');
        $this->addImage($fieldset, 'itemActions/viewGalleryIcon');
        $this->addImage($fieldset, 'itemActions/tellAFriendIcon');
        $this->addImage($fieldset, 'itemActions/addToWishlistIcon');
        $this->addImage($fieldset, 'itemActions/addToCartIcon');
        $this->addImage($fieldset, 'itemActions/viewDetailsIcon');
        $this->addImage($fieldset, 'itemActions/radioEnabledIcon');
        $this->addImage($fieldset, 'itemActions/radioDisabledIcon');
        $this->addImage($fieldset, 'itemActions/checkBoxEnabledIcon');
        $this->addImage($fieldset, 'itemActions/checkBoxDisabledIcon');
        $this->addFont($fieldset, 'itemActions/font');
        $this->addFont($fieldset, 'itemActions/radioFont');
        $this->addFont($fieldset, 'itemActions/selectFont');
        $this->addColor($fieldset, 'itemActions/relatedProductBackgroundColor');
        $this->addColor($fieldset, 'itemActions/configHeaderBackgroundColor');
        $this->addColor($fieldset, 'itemActions/configContentBackgroundColor');

        $model = Mage::registry('current_app');
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Configure image element type
     *
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_image'),
        );
    }
}
