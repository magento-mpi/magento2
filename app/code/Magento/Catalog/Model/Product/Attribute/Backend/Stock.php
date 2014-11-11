<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Attribute\Backend;

use Magento\Catalog\Model\Product;

/**
 * Quantity and Stock Status attribute processing
 */
class Stock extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Stock item service
     *
     * @var \Magento\CatalogInventory\Service\V1\StockItemService
     */
    protected $stockItemService;

    /**
     * Construct
     *
     * @param \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
     */
    public function __construct(\Magento\CatalogInventory\Service\V1\StockItemService $stockItemService)
    {
        $this->stockItemService = $stockItemService;
    }

    /**
     * Set inventory data to custom attribute
     *
     * @param Product $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $stockItemDo = $this->stockItemService->getStockItem($object->getId());
        $object->setData(
            $this->getAttribute()->getAttributeCode(),
            array('is_in_stock' => $stockItemDo->getIsInStock(), 'qty' => $stockItemDo->getQty())
        );
        return parent::afterLoad($object);
    }

    /**
     * Prepare inventory data from custom attribute
     *
     * @param Product $object
     * @return void
     */
    public function beforeSave($object)
    {
        $stockData = $object->getData($this->getAttribute()->getAttributeCode());
        if (isset($stockData['qty']) && $stockData['qty'] === '') {
            $stockData['qty'] = null;
        }
        if ($object->getStockData() !== null || $stockData !== null) {
            $object->setStockData(array_replace((array)$object->getStockData(), (array)$stockData));
        }
        $object->unsetData($this->getAttribute()->getAttributeCode());
        parent::beforeSave($object);
    }

    /**
     * Validate
     *
     * @param Product $object
     * @throws \Magento\Framework\Model\Exception
     * @return bool
     */
    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!empty($value['qty']) && !preg_match('/^-?\d*(\.|,)?\d{0,4}$/i', $value['qty'])) {
            throw new \Magento\Framework\Model\Exception(__('Please enter a valid number in this field.'));
        }
        return true;
    }
}
