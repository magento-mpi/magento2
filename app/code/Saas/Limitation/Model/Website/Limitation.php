<?php
/**
 * Functional limitation for number of websites
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Website_Limitation implements Saas_Limitation_Model_Limitation_LimitationInterface
{
    /**
     * @var Saas_Limitation_Model_Limitation_Config
     */
    private $_config;

    /**
     * @var Mage_Core_Model_Resource_Website
     */
    private $_resource;

    /**
     * @param Saas_Limitation_Model_Limitation_Config $config
     * @param Mage_Core_Model_Resource_Website $resource
     */
    public function __construct(
        Saas_Limitation_Model_Limitation_Config $config,
        Mage_Core_Model_Resource_Website $resource
    ) {
        $this->_config = $config;
        $this->_resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreshold()
    {
        return $this->_config->getThreshold('website');
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->_resource->countAll();
    }
}