<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Add by SKU" error block
 *
 * @method Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Abstract setListType()
 * @method string                                                  getListType()
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Abstract extends Magento_Adminhtml_Block_Widget
{
    /*
     * JS listType of the error grid
     */
    const LIST_TYPE = 'errors';

    /**
     * List of failed items
     *
     * @var null|array
     */
    protected $_failedItems;

    /**
     * Cart instance
     *
     * @var Enterprise_Checkout_Model_Cart|null
     */
    protected $_cart;

    protected $_template = 'sku/errors.phtml';

    /**
     * Define ID
     */
    protected function _construct()
    {
        $this->setListType(self::LIST_TYPE);

    }

    /**
     * Accordion header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('<span id="sku-attention-num">%1</span> product(s) require attention.', count($this->getFailedItems()));
    }

    /**
     * Retrieve CSS class for header
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'sku-errors';
    }

    /**
     * Retrieve "Add to order" button
     *
     * @return mixed
     */
    public function getButtonsHtml()
    {
        $buttonData = array(
            'label'   => __('Remove All'),
            'onclick' => 'addBySku.removeAllFailed()',
            'class'   => 'action-delete',
        );
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData($buttonData)->toHtml();
    }

    /**
     * Retrieve items marked as unsuccessful after prepareAddProductsBySku()
     *
     * @return array
     */
    public function getFailedItems()
    {
        if (is_null($this->_failedItems)) {
            $this->_failedItems = $this->getCart()->getFailedItems();
        }
        return $this->_failedItems;
    }

    /**
     * Retrieve url to configure item
     *
     * @return string
     */
    abstract public function getConfigureUrl();

    /**
     * Disable output of error grid in case no errors occurred
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->getFailedItems();
        if (empty($this->_failedItems)) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Implementation-specific JavaScript to be inserted into template
     *
     * @return string
     */
    public function getAdditionalJavascript()
    {
        return '';
    }

    /**
     * Retrieve cart instance
     *
     * @return Enterprise_Checkout_Model_Cart
     */
    public function getCart()
    {
        if (!isset($this->_cart)) {
            $this->_cart =  Mage::getModel('Enterprise_Checkout_Model_Cart');
        }
        return $this->_cart;
    }

    /**
     * Retrieve current store instance
     *
     * @abstract
     * @return Magento_Core_Model_Store
     */
    abstract public function getStore();

    /**
     * Get title of button, that adds products from grid
     *
     * @abstract
     * @return string
     */
    abstract public function getAddButtonTitle();
}
