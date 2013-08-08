<?php
/**
 * Functional limitation for number of stores
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_Limitation implements Saas_Limitation_Model_Limitation_LimitationInterface
{
    /**
     * @var Saas_Limitation_Model_Limitation_Config
     */
    private $_config;

    /**
     * @var Magento_Core_Model_Resource_Store
     */
    private $_resource;

    /**
     * @param Saas_Limitation_Model_Limitation_Config $config
     * @param Magento_Core_Model_Resource_Store $resource
     */
    public function __construct(
        Saas_Limitation_Model_Limitation_Config $config,
        Magento_Core_Model_Resource_Store $resource
    ) {
        $this->_config = $config;
        $this->_resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreshold()
    {
        return $this->_config->getThreshold('store');
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->_resource->countAll();
    }
}
