<?php
/**
 * Category functional limitations
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
     * Mage resource category
     *
     * @var Mage_Catalog_Model_Resource_Category
     */
    private $_resource;

    /**
     * Mage config
     *
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
     * @param int $categoriesCount number of categories to create
     * @return bool
     */
    public function isCreateRestricted($categoriesCount = 1)
    {
        $limit = (int)$this->_config->getNode(self::XML_PATH_NUM_CATEGORIES);
        if ($limit > 0) {
            return $this->_resource->countVisible() + $categoriesCount > $limit;
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
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Sorry, you are using all the categories your account allows. To add more, first delete a category or upgrade your service.');
    }
}
