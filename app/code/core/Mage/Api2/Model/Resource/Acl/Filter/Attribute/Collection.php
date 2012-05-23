<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 filter ACL attribute resource collection model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Filter_Attribute_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_Api2_Model_Acl_Filter_Attribute', 'Mage_Api2_Model_Resource_Acl_Filter_Attribute');
    }

    /**
     * Add filtering by user type
     *
     * @param string $userType
     * @return Mage_Api2_Model_Resource_Acl_Filter_Attribute_Collection
     */
    public function addFilterByUserType($userType)
    {
        $this->addFilter('user_type', $userType, 'public');
        return $this;
    }
}
