<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftCardAccount Resource Collection
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCardAccount_Model_Resource_Giftcardaccount_Grid_Website
        implements Mage_Core_Model_Option_ArrayInterface
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
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getWebsiteOptionHash();
    }
}
