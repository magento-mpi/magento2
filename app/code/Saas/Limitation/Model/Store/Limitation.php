<?php
/**
 * Functional limitation for number of stores
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_Limitation
{
    /**
     * XML-node that stores limitation of number of stores in the system
     */
    const XML_PATH_NUM_STORES = 'limitations/store';

    /**
     * @var Mage_Core_Model_Resource_Store
     */
    private $_resource;

    /**
     * @var Mage_Core_Model_Config
     */
    private $_config;

    /**
     * Determine restriction
     *
     * @param Mage_Core_Model_Resource_Store $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Resource_Store $resource, Mage_Core_Model_Config $config)
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
        $limit = (int)$this->_config->getNode(self::XML_PATH_NUM_STORES);
        if ($limit > 0) {
            return $this->_resource->countAll() >= $limit;
        }
        return false;
    }

    /**
     * User notification message about the restriction
     *
     * @return string
     */
    public function getCreateRestrictedMessage()
    {
        // @codingStandardsIgnoreStart
        return Mage::helper('Saas_Limitation_Helper_Data')->__('Sorry, you are using all the store views your account allows. To add more, first delete a store view or upgrade your service.');
        // @codingStandardsIgnoreEnd
    }
}
