<?php
/**
 * {license_notice}
 *
 * @category    Enterpise
 * @package     Enterpise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer segment data grid collection
 *
 * @category    Enterpise
 * @package     Enterpice_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Resource_Grid_Website implements Mage_Core_Model_Option_ArrayInterface
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
     * Return website array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getWebsiteOptionHash();
    }
}