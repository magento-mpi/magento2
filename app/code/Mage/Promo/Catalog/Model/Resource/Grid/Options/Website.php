<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Promo_Catalog_Model_Resource_Grid_Options_Website
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
