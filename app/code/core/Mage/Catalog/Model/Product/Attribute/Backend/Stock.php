<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quantity and Stock Status attribute processing
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Stock extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @var Mage_CatalogInventory_Model_Stock_Item
     */
    protected $_inventory;

    public function __construct(array $data = array())
    {
        $this->_inventory = isset($data['inventory'])
            ? $data['inventory']
            : Mage::getModel('Mage_CatalogInventory_Model_Stock_Item');
    }

    /**
     * Set inventory data to custom attribute
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterLoad($object)
    {
        $this->_inventory->loadByProduct($object);
        $object->setData(
            $this->getAttribute()->getAttributeCode(),
            array(
                'is_in_stock' => $this->_inventory->getIsInStock(),
                'qty' => $this->_inventory->getQty(),
            )
        );
        return parent::afterLoad($object);
    }

    /**
     * Prepare inventory data from custom atribute
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract|void
     */
    public function beforeSave($object)
    {
        $stockData = $object->getData($this->getAttribute()->getAttributeCode());
        $stockData['qty'] = !isset($stockData['qty']) || $stockData['qty'] === '' ? null : $stockData['qty'];

        $object->setStockData(array_replace((array)$object->getStockData(), (array)$stockData));
        $object->unsetData($this->getAttribute()->getAttributeCode());
        parent::beforeSave($object);
    }
}
