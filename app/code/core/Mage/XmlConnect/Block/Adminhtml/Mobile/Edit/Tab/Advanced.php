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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Advanced extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
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
        $this->setForm($form);

        $fieldset = $form->addFieldset('navigationBar', array('legend' => $this->__('Navigation Bar')));
        $this->_addElementTypes($fieldset);
        //$this->addColor($fieldset, 'conf[native][navigationBar][tintColor]', $this->__('Tint Color'));
        $this->addColor($fieldset, 'conf[native][navigationBar][backgroundColor]', $this->__('Background Color'));
        //$this->addImage($fieldset, 'conf[native][navigationBar][icon]', $this->__('Icon'));

        $fieldset = $form->addFieldset('sortingBar', array('legend' => $this->__('Sorting Bar')));
        $this->_addElementTypes($fieldset);
        $this->addColor($fieldset, 'conf[native][sortingBar][backgroundColor]', $this->__('Background Color'));
        $this->addColor($fieldset, 'conf[native][sortingBar][tintColor]', $this->__('Tint Color'));

        $fieldset = $form->addFieldset('categoryItem', array('legend' => $this->__('Category Item')));
        $this->_addElementTypes($fieldset);
        $this->addColor($fieldset, 'conf[native][categoryItem][backgroundColor]', $this->__('Background Color'));
        $this->addColor($fieldset, 'conf[native][categoryItem][tintColor]', $this->__('Tint Color'));

        $fieldset = $form->addFieldset('body_section', array('legend' => $this->__('Body')));
        $this->_addElementTypes($fieldset);
        //$this->addImage($fieldset, 'conf[native][body][bannerImage]', $this->__('Banner Image'));
        $this->addColor($fieldset, 'conf[native][body][backgroundColor]', $this->__('Background Color'));
        //$this->addImage($fieldset, 'conf[native][body][backgroundImage]', $this->__('Background Image'));
        $this->addColor($fieldset, 'conf[native][body][scrollBackgroundColor]', $this->__('Scroll Background Color'));
        //$this->addImage($fieldset, 'conf[native][body][itemBackgroundIcon]', $this->__('Item Background Icon'));
        //$this->addImage($fieldset, 'conf[native][body][rowBackgroundIcon]', $this->__('Row Background Icon'));
        $this->addImage($fieldset, 'conf[native][body][rowAttributeIcon]', $this->__('Row Attribute Icon'));
        //$this->addImage($fieldset, 'conf[native][body][addToCartBackgroundIcon]', $this->__('Add To Cart Background Icon'));
        $this->addImage($fieldset, 'conf[native][body][actionsBackgroundIcon]', $this->__('Actions Background Icon'));
        $this->addImage($fieldset, 'conf[native][body][reviewsBackgroundIcon]', $this->__('Reviews Background Icon'));

        $fieldset = $form->addFieldset('appliedFilters', array('legend' => $this->__('Applied Filters')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'conf[native][appliedFilters][backgroundImage]', $this->__('Background Image'));
        $this->addColor($fieldset, 'conf[native][appliedFilters][backgroundColor]', $this->__('Background Color'));

        $fieldset = $form->addFieldset('itemActions', array('legend' => $this->__('Item Actions')));
        $this->_addElementTypes($fieldset);
        //$this->addImage($fieldset, 'conf[native][itemActions][backgroundImage]', $this->__('Background Image'));
        $this->addImage($fieldset, 'conf[native][itemActions][viewGalleryIcon]', $this->__('View Gallery Icon'));
        $this->addImage($fieldset, 'conf[native][itemActions][tellAFriendIcon]', $this->__('Tell A Friend Icon'));
        $this->addImage($fieldset, 'conf[native][itemActions][addToWishlistIcon]', $this->__('Add To Wishlist Icon'));
        $this->addImage($fieldset, 'conf[native][itemActions][addToCartIcon]', $this->__('Add To Cart Icon'));
        $this->addImage($fieldset, 'conf[native][itemActions][viewDetailsIcon]', $this->__('View Details Icon'));
        $this->addImage($fieldset, 'conf[native][itemActions][radioEnabledIcon]', $this->__('Radio Enabled Icon'));
        $this->addImage($fieldset, 'conf[native][itemActions][radioDisabledIcon]', $this->__('Radio Disabled Icon'));
        $this->addImage($fieldset, 'conf[native][itemActions][checkBoxEnabledIcon]', $this->__('Check Box Enabled Icon'));
        $this->addImage($fieldset, 'conf[native][itemActions][checkBoxDisabledIcon]', $this->__('Check Box Disabled Icon'));
        $this->addColor($fieldset, 'conf[native][itemActions][relatedProductBackgroundColor]', $this->__('Related Product Background Color'));
        $this->addColor($fieldset, 'conf[native][itemActions][configHeaderBackgroundColor]', $this->__('Config Header Background Color'));
        $this->addColor($fieldset, 'conf[native][itemActions][configContentBackgroundColor]', $this->__('Config Content Background Color'));

        /*
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
        */

        $model = Mage::registry('current_app');
        $form->setValues($model->getFormData());
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Advanced settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Advanced settings');
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
     * @return false
     */
    public function isHidden()
    {
        return false;
    }
}
