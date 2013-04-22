<?php
/**
 * Functional limitation on total number of websites in the system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Website_Limitation
{
    /**
     * XML-node that stores limitation of number of websites in the system
     */
    const XML_PATH_NUM_PRODUCTS = 'limitations/website';

    /**
     * @var Mage_Core_Model_Resource_Website
     */
    private $_resource;

    /**
     * @var Mage_Core_Model_Config
     */
    private $_config;

    /**
     * Inject dependencies
     *
     * @param Mage_Core_Model_Resource_Website $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Resource_Website $resource, Mage_Core_Model_Config $config)
    {
        $this->_resource = $resource;
        $this->_config = $config;
    }

    /**
     * Whether adding new entity is restricted
     *
     * @return bool
     */
    public function isCreateRestricted()
    {
        $limit = (int)$this->_config->getNode(self::XML_PATH_NUM_PRODUCTS);
        if ($limit > 0) {
            return $this->_resource->countAll() >= $limit;
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
        return Mage::helper('Mage_Core_Helper_Data')->__('Maximum allowed number of websites is reached.');
    }
}
