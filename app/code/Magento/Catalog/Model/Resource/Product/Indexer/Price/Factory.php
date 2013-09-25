<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource product indexer price factory
 */
class Magento_Catalog_Model_Resource_Product_Indexer_Price_Factory
{
    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create indexer price
     *
     * @param string $className
     * @param array $data
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Price_Default
     * @throws Magento_Core_Exception
     */
    public function create($className, array $data = array())
    {
        $indexerPrice = $this->_objectManager->create($className, $data);

        if (!$indexerPrice instanceof Magento_Catalog_Model_Resource_Product_Indexer_Price_Default) {
            throw new Magento_Core_Exception($className
                . ' doesn\'t extends Magento_Catalog_Model_Resource_Product_Indexer_Price_Default');
        }
        return $indexerPrice;
    }
}
