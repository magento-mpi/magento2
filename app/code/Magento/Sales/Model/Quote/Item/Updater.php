<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote\Item;

use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\Locale\FormatInterface;
use \Magento\Sales\Model\Quote;
use \Magento\Framework\Object\Factory as ObjectFactory;
use \Magento\Sales\Model\Quote\Item;
use Zend\Code\Exception\InvalidArgumentException;

/**
 * Class Updater
 */
class Updater
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * @param ProductFactory $productFactory
     * @param FormatInterface $localeFormat
     * @param ObjectFactory $objectFactory
     */
    public function __construct(
        ProductFactory $productFactory,
        FormatInterface $localeFormat,
        ObjectFactory $objectFactory
    ) {
        $this->productFactory = $productFactory;
        $this->localeFormat = $localeFormat;
        $this->objectFactory = $objectFactory;
    }

    /**
     * Update quote item qty.
     * Custom price is updated in case 'custom_price' value exists
     *
     * @param $item
     * @param array $info
     * @throws InvalidArgumentException
     * @return Updater
     */
    public function update(Item $item, array $info)
    {
        if (!isset($info['qty'])) {
            throw new InvalidArgumentException(__('The qty value is required to update quote item.'));
        }
        $itemQty = $info['qty'];
        if ($item->getProduct()->getStockItem()) {
            if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                $itemQty = (int)$info['qty'];
            } else {
                $item->setIsQtyDecimal(1);
            }
        }
        $itemQty = $itemQty > 0 ? $itemQty : 1;
        if (isset($info['custom_price'])) {
            $this->prepareCustomPrice($info, $item);
        }

        if (empty($info['action']) || !empty($info['configured'])) {
            $noDiscount = !isset($info['use_discount']);
            $item->setQty($itemQty);
            $item->setNoDiscount($noDiscount);
            $item->getProduct()->setIsSuperMode(true);
            $item->getProduct()->unsSkipCheckRequiredOption();
            $item->checkData();
        }

        return $this;
    }

    /**
     * Prepares custom price and sets into a BuyRequest object as option of quote item
     *
     * @param array $info
     * @param Item $item
     * @return array
     */
    protected function prepareCustomPrice(array $info, Item $item)
    {
        $itemPrice = $this->parseCustomPrice($info['custom_price']);
        $infoBuyRequest = $item->getBuyRequest();
        if ($infoBuyRequest) {
            $infoBuyRequest->setCustomPrice($itemPrice);

            $infoBuyRequest->setValue(serialize($infoBuyRequest->getData()));
            $infoBuyRequest->setCode('info_buyRequest');
            $infoBuyRequest->setProduct($item->getProduct());

            $item->addOption($infoBuyRequest);
        }

        $item->setCustomPrice($itemPrice);
        $item->setOriginalCustomPrice($itemPrice);
    }

    /**
     * Return formatted price
     *
     * @param float|int $price
     * @return float|int
     */
    protected function parseCustomPrice($price)
    {
        $price = $this->localeFormat->getNumber($price);
        return $price > 0 ? $price : 0;
    }
}
