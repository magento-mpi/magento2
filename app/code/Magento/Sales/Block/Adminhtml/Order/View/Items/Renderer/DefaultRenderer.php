<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml sales order item renderer
 *
 * @category   Magento
 * @package    Magento_Sales
 */
namespace Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer;

class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{
    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $_messageHelper;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Registry $registry
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Registry $registry,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        array $data = array()
    ) {
        $this->_checkoutHelper = $checkoutHelper;
        $this->_messageHelper = $messageHelper;
        parent::__construct($context, $productFactory, $registry, $data);
    }

    public function getItem()
    {
        return $this->_getData('item');
    }

    /**
     * Retrieve real html id for field
     *
     * @param string $name
     * @return string
     */
    public function getFieldId($id)
    {
        return $this->getFieldIdPrefix() . $id;
    }

    /**
     * Retrieve field html id prefix
     *
     * @return string
     */
    public function getFieldIdPrefix()
    {
        return 'order_item_' . $this->getItem()->getId() . '_';
    }

    /**
     * Indicate that block can display container
     *
     * @return bool
     */
    public function canDisplayContainer()
    {
        return $this->getRequest()->getParam('reload') != 1;
    }

    /**
     * Giftmessage object
     *
     * @var \Magento\GiftMessage\Model\Message
     */
    protected $_giftMessage = array();

    /**
     * Retrieve default value for giftmessage sender
     *
     * @return string
     */
    public function getDefaultSender()
    {
        if(!$this->getItem()) {
            return '';
        }

        if($this->getItem()->getOrder()) {
            return $this->getItem()->getOrder()->getBillingAddress()->getName();
        }

        return $this->getItem()->getBillingAddress()->getName();
    }

    /**
     * Retrieve default value for giftmessage recipient
     *
     * @return string
     */
    public function getDefaultRecipient()
    {
        if(!$this->getItem()) {
            return '';
        }

        if($this->getItem()->getOrder()) {
            if ($this->getItem()->getOrder()->getShippingAddress()) {
                return $this->getItem()->getOrder()->getShippingAddress()->getName();
            } else if ($this->getItem()->getOrder()->getBillingAddress()) {
                return $this->getItem()->getOrder()->getBillingAddress()->getName();
            }
        }

        if ($this->getItem()->getShippingAddress()) {
            return $this->getItem()->getShippingAddress()->getName();
        } else if ($this->getItem()->getBillingAddress()) {
            return $this->getItem()->getBillingAddress()->getName();
        }

        return '';
    }

    /**
     * Retrieve real name for field
     *
     * @param string $name
     * @return string
     */
    public function getFieldName($name)
    {
        return 'giftmessage[' . $this->getItem()->getId() . '][' . $name . ']';
    }

    /**
     * Initialize gift message for entity
     *
     * @return $this
     */
    protected function _initMessage()
    {
        $this->_giftMessage[$this->getItem()->getGiftMessageId()] =
            $this->_messageHelper->getGiftMessage($this->getItem()->getGiftMessageId());

        // init default values for giftmessage form
        if(!$this->getMessage()->getSender()) {
            $this->getMessage()->setSender($this->getDefaultSender());
        }
        if(!$this->getMessage()->getRecipient()) {
            $this->getMessage()->setRecipient($this->getDefaultRecipient());
        }

        return $this;
    }

    /**
     * Retrieve gift message for entity
     *
     * @return \Magento\GiftMessage\Model\Message
     */
    public function getMessage()
    {
        if(!isset($this->_giftMessage[$this->getItem()->getGiftMessageId()])) {
            $this->_initMessage();
        }

        return $this->_giftMessage[$this->getItem()->getGiftMessageId()];
    }

    /**
     * Retrieve save url
     *
     * @return array
     */
    public function getSaveUrl()
    {
        return $this->getUrl('sales/order_view_giftmessage/save', array(
            'entity'    => $this->getItem()->getId(),
            'type'      => 'order_item',
            'reload'    => true
        ));
    }

    /**
     * Retrieve block html id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return substr($this->getFieldIdPrefix(), 0, -1);
    }

    /**
     * Indicates that block can display giftmessages form
     *
     * @return bool
     */
    public function canDisplayGiftmessage()
    {
        return $this->_messageHelper->getIsMessagesAvailable(
            'order_item', $this->getItem(), $this->getItem()->getOrder()->getStoreId()
        );
    }

    /**
     * Display susbtotal price including tax
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        return $this->displayPrices(
            $this->_checkoutHelper->getBaseSubtotalInclTax($item),
            $this->_checkoutHelper->getSubtotalInclTax($item)
        );
    }

    /**
     * Display item price including tax
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string
     */
    public function displayPriceInclTax(\Magento\Object $item)
    {
        return $this->displayPrices(
            $this->_checkoutHelper->getBasePriceInclTax($item),
            $this->_checkoutHelper->getPriceInclTax($item)
        );
    }

}
