<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Order\Item\Renderer;

use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\CreditMemo\Item as CreditMemoItem;

/**
 * Order item render block
 */
class DefaultRenderer extends \Magento\Framework\View\Element\Template
{
    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\String
     */
    protected $string;

    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $_productOptionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\String $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        array $data = array()
    ) {
        $this->string = $string;
        $this->_productOptionFactory = $productOptionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Object $item
     * @return $this
     */
    public function setItem(\Magento\Framework\Object $item)
    {
        $this->setData('item', $item);
        return $this;
    }

    /**
     * @return array|null
     */
    public function getItem()
    {
        return $this->_getData('item');
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getOrderItem()->getOrder();
    }

    /**
     * @return array|null
     */
    public function getOrderItem()
    {
        if ($this->getItem() instanceof \Magento\Sales\Model\Order\Item) {
            return $this->getItem();
        } else {
            return $this->getItem()->getOrderItem();
        }
    }

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $result = array();
        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     *
     * @return array
     */
    public function getFormatedOptionValue($optionValue)
    {
        $optionInfo = array();

        // define input data format
        if (is_array($optionValue)) {
            if (isset($optionValue['option_id'])) {
                $optionInfo = $optionValue;
                if (isset($optionInfo['value'])) {
                    $optionValue = $optionInfo['value'];
                }
            } elseif (isset($optionValue['value'])) {
                $optionValue = $optionValue['value'];
            }
        }

        // render customized option view
        if (isset($optionInfo['custom_view']) && $optionInfo['custom_view']) {
            $_default = array('value' => $optionValue);
            if (isset($optionInfo['option_type'])) {
                try {
                    $group = $this->_productOptionFactory->create()->groupFactory($optionInfo['option_type']);
                    return array('value' => $group->getCustomizedView($optionInfo));
                } catch (\Exception $e) {
                    return $_default;
                }
            }
            return $_default;
        }

        // truncate standard view
        $result = array();
        if (is_array($optionValue)) {
            $truncatedValue = implode("\n", $optionValue);
            $truncatedValue = nl2br($truncatedValue);
            return array('value' => $truncatedValue);
        } else {
            $truncatedValue = $this->filterManager->truncate($optionValue, array('length' => 55, 'etc' => ''));
            $truncatedValue = nl2br($truncatedValue);
        }

        $result = array('value' => $truncatedValue);

        if ($this->string->strlen($optionValue) > 55) {
            $result['value'] = $result['value'] . ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>';
            $optionValue = nl2br($optionValue);
            $result = array_merge($result, array('full_view' => $optionValue));
        }

        return $result;
    }

    /**
     * Return sku of order item.
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getItem()->getSku();
    }

    /**
     * Return product additional information block
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * Prepare SKU
     *
     * @param string $sku
     * @return string
     */
    public function prepareSku($sku)
    {
        return $this->escapeHtml($this->string->splitInjection($sku));
    }

    /**
     * Return item unit price html
     *
     * @return string
     */
    public function getItemPriceHtml()
    {
        $block = $this->getLayout()->getBlock('item_unit_price');
        $block->setItem($this->getItem());
        return $block->toHtml();
    }

    /**
     * Return item row total html
     *
     * @return string
     */
    public function getItemRowTotalHtml()
    {
        $block = $this->getLayout()->getBlock('item_row_total');
        $block->setItem($this->getItem());
        return $block->toHtml();
    }

    /**
     * Return the total amount minus discount
     *
     * @param OrderItem|InvoiceItem|CreditmemoItem $_item
     * @return mixed
     */
    public function getTotalAmount($_item)
    {
        $totalAmount = $_item->getRowTotal()
            - $_item->getDiscountAmount();

        return $totalAmount;
    }
}
