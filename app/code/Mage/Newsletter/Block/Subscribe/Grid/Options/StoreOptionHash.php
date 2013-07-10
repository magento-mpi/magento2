<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter options
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Newsletter_Block_Subscribe_Grid_Options_StoreOptionHash implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * System Store Model
     *
     * @var Mage_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Mage_Core_Model_System_Store
     */
    public function __construct(Mage_Core_Model_System_Store $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return store array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getStoreOptionHash();
    }
}
