<?php
/**
 * Product functional limitations
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Model_Category_Limitation
{
    /**
     * XML-node that stores limitation of number of categories in the system
     */
    const XML_PATH_NUM_CATEGORIES = 'limitations/catalog_category';

    /**
     * @var Mage_Catalog_Model_Resource_Category
     */
    private $_resource;

    /**
     * @var Mage_Core_Model_Config
     */
    private $_config;

    /**
     * Inject dependencies
     *
     * @param Mage_Catalog_Model_Resource_Category $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Catalog_Model_Resource_Category $resource, Mage_Core_Model_Config $config)
    {
        $this->_resource = $resource;
        $this->_config = $config;
    }

    /**
     * Whether creation is restricted
     *
     * @param int $num Number of products to create
     * @return bool
     */
    public function isCreateRestricted($num = 1)
    {
        $limit = (int)$this->_config->getNode(self::XML_PATH_NUM_CATEGORIES);
        if ($limit > 0) {
            return $this->_resource->countAll() + $num > $limit;
        }
        return false;
    }

    /**
     * Whether adding new product is restricted
     *
     * @return bool
     */
    public function isNewRestricted()
    {
        $limit = (int)$this->_config->getNode(self::XML_PATH_NUM_CATEGORIES);
        if ($limit > 0) {
            return $this->_resource->countAll() + 1 >= $limit;
        }
        return false;
    }

    /**
     * Get message with the the restriction explanation
     *
     * @return string
     */
    public function getCreateRestrictedMessage()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Maximum allowed number of categories is reached.');
    }
}
