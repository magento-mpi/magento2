<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping order create items info block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\Create;

class Items
    extends \Magento\GiftWrapping\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Select element for choosing gift wrapping design
     *
     * @return array
     */
    public function getDesignSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento\Core\Block\Html\Select')
            ->setData(array(
                'id'    => 'giftwrapping_design_item',
                'class' => 'select'
            ))
            ->setOptions($this->getDesignCollection()->toOptionArray());
        return $select->getHtml();
    }

    /**
     * Prepare and return quote items info
     *
     * @return \Magento\Object
     */
    public function getItemsInfo()
    {
        $data = array();
        foreach ($this->getQuote()->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($this->getDisplayGiftWrappingForItem($item)) {
                $temp = array();
                if ($price = $item->getProduct()->getGiftWrappingPrice()) {
                    if ($this->getDisplayWrappingBothPrices()) {
                        $temp['price_incl_tax'] = $this->calculatePrice(new \Magento\Object(), $price, true);
                        $temp['price_excl_tax'] = $this->calculatePrice(new \Magento\Object(), $price);
                    } else {
                        $temp['price'] = $this->calculatePrice(new \Magento\Object(), $price,
                            $this->getDisplayWrappingPriceInclTax()
                        );
                    }
                }
                $temp['design'] = $item->getGwId();
                $data[$item->getId()] = $temp;
            }
        }
        return new \Magento\Object($data);
    }

    /**
     * Check ability to display gift wrapping for items during backend order create
     *
     * @return bool
     */
    public function canDisplayGiftWrappingForItems()
    {
        $canDisplay = false;
        $count = count($this->getDesignCollection());
        if ($count) {
            foreach ($this->getQuote()->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($this->getDisplayGiftWrappingForItem($item)) {
                    $canDisplay = true;
                }
            }
        }
        return $canDisplay;
    }

    /**
     * Check ability to display gift wrapping for quote item
     *
     * @param \Magento\Sales\Model\Quote\Item $item
     * @return bool
     */
    public function getDisplayGiftWrappingForItem($item)
    {
        $allowed = $item->getProduct()->getGiftWrappingAvailable();
        return $this->_giftWrappingData
            ->isGiftWrappingAvailableForProduct($allowed, $this->getStoreId());
    }
}
