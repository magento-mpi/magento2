<?php
/**
 * Limitation of number of products in the system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Limitation
{
    /**
     * XML-node that stores limitation of number of products in the system
     */
    const XML_PATH_NUM_PRODUCTS = 'limitations/catalog_product';

    /**
     * @var Mage_Catalog_Model_Resource_Product
     */
    private $_resource;

    /**
     * @var Mage_Core_Model_Config
     */
    private $_config;

    /**
     * @param Mage_Catalog_Model_Resource_Product $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Catalog_Model_Resource_Product $resource, Mage_Core_Model_Config $config)
    {
        $this->_resource = $resource;
        $this->_config = $config;
    }

    /**
     * Whether creation of specified number of products is restricted
     *
     * @param int $number Number of products to create
     * @return bool
     */
    public function isCreateRestricted($number = 1)
    {
        $limit = $this->getLimit();
        if ($limit > 0) {
            return $this->_resource->countAll() + $number > $limit;
        }
        return false;
    }

    /**
     * Returns limit for product creation
     *
     * @return int
     */
    public function getLimit()
    {
        return (int)$this->_config->getNode(self::XML_PATH_NUM_PRODUCTS);
    }
}
