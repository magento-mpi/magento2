<?php
/**
 * Functional limitation for number of store groups
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_Group_Limitation
{
    /**
     * XML-node that stores limitation of number of store groups in the system
     */
    const XML_PATH_NUM_STORE_GROUPS = 'limitations/store_group';

    /**
     * Store group resource model
     *
     * @var Mage_Core_Model_Resource_Store_Group
     */
    private $_resource;

    /**
     * @var Mage_Core_Model_Config
     */
    private $_config;

    /**
     * @param Mage_Core_Model_Resource_Store_Group $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Resource_Store_Group $resource, Mage_Core_Model_Config $config)
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
        $limit = (int)$this->_config->getNode(self::XML_PATH_NUM_STORE_GROUPS);
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
        return Mage::helper('Saas_Limitation_Helper_Data')->__('You are using the maximum number of stores allowed.');
    }
}
