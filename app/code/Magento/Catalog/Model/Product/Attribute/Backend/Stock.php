<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quantity and Stock Status attribute processing
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Backend;

use Magento\Catalog\Model\Product;

class Stock extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Stock item factory
     *
     * @var \Magento\CatalogInventory\Model\Stock\ItemFactory
     */
    protected $_stockItemFactory;

    /**
     * Construct
     *
     * @param \Magento\Logger $logger
     * @param \Magento\CatalogInventory\Model\Stock\ItemFactory $stockItemFactory
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\CatalogInventory\Model\Stock\ItemFactory $stockItemFactory
    ) {
        $this->_stockItemFactory = $stockItemFactory;
        parent::__construct($logger);
    }

    /**
     * Set inventory data to custom attribute
     *
     * @param Product $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $item = $this->_stockItemFactory->create();
        $item->loadByProduct($object);
        $object->setData(
            $this->getAttribute()->getAttributeCode(),
            array(
                'is_in_stock' => $item->getIsInStock(),
                'qty' => $item->getQty(),
            )
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
     * @throws \Magento\Model\Exception
     * @return bool
     */
    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!empty($value['qty']) && !preg_match('/^-?\d*(\.|,)?\d{0,4}$/i', $value['qty'])) {
            throw new \Magento\Model\Exception(
                __('Please enter a valid number in this field.')
            );
        }
        return true;
    }
}
