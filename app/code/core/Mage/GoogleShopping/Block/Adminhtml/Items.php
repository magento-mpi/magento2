<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Google Content Items Grids Container
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Block_Adminhtml_Items extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('items.phtml');
    }

    /**
     * Preparing layout
     *
     * @return Mage_GoogleShopping_Block_Adminhtml_Items
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'item',
            $this->getLayout()->createBlock('Mage_GoogleShopping_Block_Adminhtml_Items_Item')
        );
        $this->setChild(
            'product',
            $this->getLayout()->createBlock('Mage_GoogleShopping_Block_Adminhtml_Items_Product')
        );
        $this->setChild(
            'store_switcher',
            $this->getLayout()->createBlock('Mage_GoogleShopping_Block_Adminhtml_Store_Switcher')
        );

        return $this;
    }

    /**
     * Get HTML code for button View Available Products
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButtonData = array(
            'id'    => 'products_grid_button',
            'label' => $this->__('View Available Products'),
        );
        return $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData($addButtonData)
            ->toHtml();
    }

    /**
     * Get HTML code for Store Switcher select
     *
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Get HTML code for CAPTCHA
     *
     * @return string
     */
    public function getCaptchaHtml()
    {
        return $this->getLayout()->createBlock('Mage_GoogleShopping_Block_Adminhtml_Captcha')
            ->setGcontentCaptchaToken($this->getGcontentCaptchaToken())
            ->setGcontentCaptchaUrl($this->getGcontentCaptchaUrl())
            ->toHtml();
    }

    /**
     * Get selecetd store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_getData('store');
    }
}
