<?php
/**
 * Limitation on the number of categories in the system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Category_Limitation implements Saas_Limitation_Model_Limitation_LimitationInterface
{
    /**
     * @var Saas_Limitation_Model_Limitation_Config
     */
    private $_config;

    /**
     * @var Mage_Catalog_Model_Resource_Category
     */
    private $_resource;

    /**
     * @param Saas_Limitation_Model_Limitation_Config $config
     * @param Mage_Catalog_Model_Resource_Category $resource
     */
    public function __construct(
        Saas_Limitation_Model_Limitation_Config $config,
        Mage_Catalog_Model_Resource_Category $resource
    ) {
        $this->_config = $config;
        $this->_resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreshold()
    {
        return $this->_config->getThreshold('catalog_category');
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->_resource->countVisible();
    }
}
