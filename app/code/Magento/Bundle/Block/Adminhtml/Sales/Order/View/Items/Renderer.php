<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Block\Adminhtml\Sales\Order\View\Items;

/**
 * Adminhtml sales order item renderer
 */
class Renderer extends \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer
{
    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate($value, array(
            'length' => $length,
            'etc' => $etc,
            'remainder' => $remainder,
            'breakWords' => $breakWords
        ));
    }

    /**
     * @param null|object $item
     * @return bool
     */
    public function isShipmentSeparately($item = null)
    {
        if ($item) {
            $parentItem = $item->getParentItem();
            if ($parentItem) {
                $options = $parentItem->getProductOptions();
                if ($options) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == \Magento\Catalog\Model\Product\Type\AbstractType::SHIPMENT_SEPARATELY
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $options = $item->getProductOptions();
                if ($options) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == \Magento\Catalog\Model\Product\Type\AbstractType::SHIPMENT_SEPARATELY
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['shipment_type'])
                && $options['shipment_type'] == \Magento\Catalog\Model\Product\Type\AbstractType::SHIPMENT_SEPARATELY
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param null|object $item
     * @return bool
     */
    public function isChildCalculated($item = null)
    {
        if ($item) {
            $parentItem = $item->getParentItem();
            if ($parentItem) {
                $options = $parentItem->getProductOptions();
                if ($options) {
                    if (isset($options['product_calculations'])
                        && $options['product_calculations'] == \Magento\Catalog\Model\Product\Type\AbstractType::CALCULATE_CHILD
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $options = $item->getProductOptions();
                if ($options) {
                    if (isset($options['product_calculations'])
                        && $options['product_calculations'] == \Magento\Catalog\Model\Product\Type\AbstractType::CALCULATE_CHILD
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        $options = $this->getItem()->getProductOptions();
        if ($options) {
            if (isset($options['product_calculations'])
                && $options['product_calculations'] == \Magento\Catalog\Model\Product\Type\AbstractType::CALCULATE_CHILD
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    public function getSelectionAttributes($item)
    {
        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }
        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getOrderOptions()
    {
        $result = array();
        $options = $this->getItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    /**
     * @param object $item
     * @return string
     */
    public function getValueHtml($item)
    {
        $result = $this->escapeHtml($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }
        if (!$this->isChildCalculated($item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result .= " " . $this->getItem()->getOrder()->formatPrice($attributes['price']);
            }
        }
        return $result;
    }

    /**
     * @param object $item
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        if (($item->getParentItem() && $this->isChildCalculated())
            || (!$item->getParentItem() && !$this->isChildCalculated())
        ) {
            return true;
        }
        return false;
    }
}
