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
     * Store group resource model
     *
     * @var Mage_Core_Model_Resource_Store_Group
     */
    private $_resource;

    /**
     * Allowed quantity
     *
     * @var int
     */
    private $_allowedQty = null;

    /**
     * Is entity quantity limited
     *
     * @var bool
     */
    private $_isLimited = false;

    /**
     * Determine restriction
     *
     * @param Mage_Core_Model_Resource_Store_Group $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Resource_Store_Group $resource, Mage_Core_Model_Config $config)
    {
        $this->_resource = $resource;

        $allowedQty = (string)$config->getNode('limitations/store_group');
        if ('' === $allowedQty) {
            return;
        }
        $this->_allowedQty = (int)$allowedQty;
        $this->_isLimited = true;
    }

    /**
     * Whether it is permitted to create new items
     *
     * @return bool
     */
    public function canCreate()
    {
        if ($this->_isLimited) {
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
