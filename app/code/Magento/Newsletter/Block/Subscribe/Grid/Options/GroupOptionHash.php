<?php
/**
 * Newsletter group options
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Newsletter_Block_Subscribe_Grid_Options_GroupOptionHash
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * System Store Model
     *
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Magento_Core_Model_System_Store
     */
    public function __construct(Magento_Core_Model_System_Store $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return store group array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getStoreGroupOptionHash();
    }
}
