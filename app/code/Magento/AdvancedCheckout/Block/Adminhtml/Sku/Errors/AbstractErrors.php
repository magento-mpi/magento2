<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Add by SKU" error block
 *
 * @method \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors setListType()
 * @method string                                                  getListType()
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors;

abstract class AbstractErrors extends \Magento\Backend\Block\Widget
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
     * @var \Magento\AdvancedCheckout\Model\Cart|null
     */
    protected $_cart;

    protected $_template = 'sku/errors.phtml';

    /**
     * Advanced checkout cart factory
     *
     * @var \Magento\AdvancedCheckout\Model\CartFactory
     */
    protected $_cartFactory = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\AdvancedCheckout\Model\CartFactory $cartFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\AdvancedCheckout\Model\CartFactory $cartFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_cartFactory = $cartFactory;
    }

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
        return $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData($buttonData)->toHtml();
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
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function getCart()
    {
        if (!isset($this->_cart)) {
            $this->_cart =  $this->_cartFactory->create();
        }
        return $this->_cart;
    }

    /**
     * Retrieve current store instance
     *
     * @abstract
     * @return \Magento\Core\Model\Store
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
