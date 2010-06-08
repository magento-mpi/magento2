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
        $this->addColor($fieldset, 'conf[native][navigationBar][tintColor]');
        $this->addColor($fieldset, 'conf[native][navigationBar][backgroundColor]');
        //$this->addImage($fieldset, 'conf[native][navigationBar][icon]');
        $this->addFont($fieldset, 'conf[native][navigationBar][font]');

        $fieldset = $form->addFieldset('sortingBar', array('legend' => $this->__('Sorting Bar')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'conf[native][sortingBar][backgroundImage]');
        $this->addColor($fieldset, 'conf[native][sortingBar][tintColor]');
        $this->addFont($fieldset, 'conf[native][sortingBar][font]');

        $fieldset = $form->addFieldset('body_section', array('legend' => $this->__('Body')));
        $this->_addElementTypes($fieldset);
        //$this->addImage($fieldset, 'conf[native][body][bannerImage]');
        $this->addColor($fieldset, 'conf[native][body][backgroundColor]');
        $this->addImage($fieldset, 'conf[native][body][backgroundImage]');
        $this->addColor($fieldset, 'conf[native][body][scrollBackgroundColor]');
        $this->addImage($fieldset, 'conf[native][body][itemBackgroundIcon]');
        $this->addImage($fieldset, 'conf[native][body][rowBackgroundIcon]');
        $this->addImage($fieldset, 'conf[native][body][rowAttributeIcon]');
        $this->addImage($fieldset, 'conf[native][body][addToCartBackgroundIcon]');
        $this->addImage($fieldset, 'conf[native][body][actionsBackgroundIcon]');
        $this->addImage($fieldset, 'conf[native][body][reviewsBackgroundIcon]');
        $this->addFont($fieldset,  'conf[native][body][categoryItemFont]');
        $this->addFont($fieldset,  'conf[native][body][copyrightFont]');
        $this->addFont($fieldset,  'conf[native][body][versionFont]');
        $this->addFont($fieldset,  'conf[native][body][productButtonFont]');
        $this->addFont($fieldset,  'conf[native][body][nameFont]');
        $this->addFont($fieldset,  'conf[native][body][priceFont]');
        $this->addFont($fieldset,  'conf[native][body][plainFont]');
        $this->addFont($fieldset,  'conf[native][body][textFont]');
        $this->addFont($fieldset,  'conf[native][body][ratingHeaderFont]');

        $fieldset = $form->addFieldset('filters', array('legend' => $this->__('Filters')));
        $this->_addElementTypes($fieldset);
        $this->addFont($fieldset,  'conf[native][filters][nameFont]');
        $this->addFont($fieldset,  'conf[native][filters][valueFont]');

        $fieldset = $form->addFieldset('appliedFilters', array('legend' => $this->__('Applied Filters')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'conf[native][appliedFilters][backgroundImage]');
        $this->addColor($fieldset, 'conf[native][appliedFilters][backgroundColor]');
        $this->addFont($fieldset,  'conf[native][appliedFilters][font]');
        $this->addFont($fieldset,  'conf[native][appliedFilters][counfFont]');
        $this->addFont($fieldset,  'conf[native][appliedFilters][titleFont]');

        $fieldset = $form->addFieldset('itemActions', array('legend' => $this->__('Item Actions')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'conf[native][itemActions][backgroundImage]');
        $this->addImage($fieldset, 'conf[native][itemActions][viewGalleryIcon]');
        $this->addImage($fieldset, 'conf[native][itemActions][tellAFriendIcon]');
        $this->addImage($fieldset, 'conf[native][itemActions][addToWishlistIcon]');
        $this->addImage($fieldset, 'conf[native][itemActions][addToCartIcon]');
        $this->addImage($fieldset, 'conf[native][itemActions][viewDetailsIcon]');
        $this->addImage($fieldset, 'conf[native][itemActions][radioEnabledIcon]');
        $this->addImage($fieldset, 'conf[native][itemActions][radioDisabledIcon]');
        $this->addImage($fieldset, 'conf[native][itemActions][checkBoxEnabledIcon]');
        $this->addImage($fieldset, 'conf[native][itemActions][checkBoxDisabledIcon]');
        $this->addFont($fieldset,  'conf[native][itemActions][font]');
        $this->addFont($fieldset,  'conf[native][itemActions][radioFont]');
        $this->addFont($fieldset,  'conf[native][itemActions][selectFont]');
        $this->addColor($fieldset, 'conf[native][itemActions][relatedProductBackgroundColor]');
        $this->addColor($fieldset, 'conf[native][itemActions][configHeaderBackgroundColor]');
        $this->addColor($fieldset, 'conf[native][itemActions][configContentBackgroundColor]');

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
