<?php
/**
 * Functional limitation for number of stores
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Store_Limitation
{
    /**
     * @var int
     */
    private $_totalQty = 0;

    /**
     * @var int
     */
    private $_allowedQty = 0;

    /**
     * @var bool
     */
    private $_isRestricted = false;

    /**
     * Determine restriction
     *
     * @param Mage_Core_Model_Resource_Store $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Resource_Store $resource, Mage_Core_Model_Config $config)
    {
        $allowedQty = (string)$config->getNode('global/functional_limitation/max_store_count');
        if ('' === $allowedQty) {
            return;
        }
        $this->_totalQty = $resource->countAll();
        $this->_allowedQty = (int)$allowedQty;
        $this->_isRestricted = true;
    }

    /**
     * Whether restriction of creating new items is effective
     *
     * @return bool
     */
    public function isCreateRestricted()
    {
        if ($this->_isRestricted) {
            // the store "admin" store is not visible to the user, so it doesn't count
            return ($this->_totalQty - 1) >= $this->_allowedQty;
        }
        return false;
    }

    /**
     * User notification message about the restriction
     *
     * @param Mage_Core_Helper_Abstract $helper
     * @return string
     */
    public static function getCreateRestrictionMessage(Mage_Core_Helper_Abstract $helper)
    {
        return $helper->__('You are using the maximum number of store views allowed.');
    }
}
