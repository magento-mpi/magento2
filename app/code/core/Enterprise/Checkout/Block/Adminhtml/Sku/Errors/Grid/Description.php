<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Block with description of why item has not been added to ordered items list
 *
 * @method Varien_Object                                                   getItem()
 * @method Mage_Catalog_Model_Product                                      getProduct()
 * @method Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Description setItem()
 * @method Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Description setProduct()
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Description extends Mage_Adminhtml_Block_Template
{
    /**
     * List of error messages
     *
     * @var array
     */
    protected $_errorMessages = array();

    /**
     * Define error messages and template
     */
    public function __construct()
    {
        $this->setTemplate('enterprise/checkout/sku/errors/grid/description.phtml');
        $this->_errorMessages = array(
            Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU => $this->__('SKU not found in catalog'),
            Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_CONFIGURE => $this->__('Product requires configuration'),
            Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK => $this->__('Out of stock'),
            Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED => $this->__('Requested quantity is not available')
        );
    }

    /**
     * Return error message by its code
     *
     * @see Enterprise_Checkout_Helper_Data ADD_ITEM_STATUS_* constants
     * @param string $code
     * @return string
     */
    public function getErrorMessageByCode($code)
    {
        return isset($this->_errorMessages[$code]) ? $this->_errorMessages[$code] : '';
    }

    /**
     * HTML code of "Configure" button
     *
     * @return string
     */
    public function getConfigureButtonHtml()
    {
        $canConfigure = $this->getProduct()->canConfigure();
        /* @var $button Mage_Adminhtml_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button', '', array(
            'class'    => $canConfigure ? '' : 'disabled',
            'onclick'  => $canConfigure ? "addBySku.configure({$this->getProduct()->getId()})" : '',
            'disabled' => !$canConfigure,
            'label'    => Mage::helper('enterprise_checkout')->__('Configure'),
        ));

        return $button->toHtml();
    }

    /**
     * Retrieve HTML name for element
     *
     * @return string
     */
    public function getSourceId()
    {
        return $this->_prepareLayout()->getLayout()->getBlock('sku_error_grid')->getId();
    }
}
