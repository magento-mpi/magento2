<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Source_Website_OptionHash
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
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getWebsiteOptionHash();
    }
}


