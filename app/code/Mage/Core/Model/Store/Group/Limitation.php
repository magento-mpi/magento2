<?php
/**
 * Functional limitation for number of stores
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Store_Group_Limitation
{
    /**
     * @var Mage_Core_Model_Resource_Store_Group
     */
    private $_resource;

    /**
     * @var int
     */
    private $_allowedQty = 0;

    /**
     * Determine restriction
     *
     * @param Mage_Core_Model_Resource_Store_Group $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Resource_Store_Group $resource, Mage_Core_Model_Config $config)
    {
        $this->_resource = $resource;
        $this->_allowedQty = (int)$config->getNode('limitations/store_group');
    }

    /**
     * Whether it is permitted to create new items
     *
     * @return bool
     */
    public function canCreate()
    {
        if ($this->_allowedQty > 0) {
            return $this->_resource->countAll() < $this->_allowedQty;
        }
        return true;
    }

    /**
     * User notification message about the restriction
     *
     * @return string
     */
    public static function getCreateRestrictionMessage()
    {
        return Mage::helper('Mage_Core_Helper_Data')->__('You are using the maximum number of stores allowed.');
    }
}
