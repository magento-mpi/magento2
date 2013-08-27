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
 * GiftCardAccount Pool Resource Model Collection
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCardAccount_Model_Resource_Pool_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_GiftCardAccount_Model_Pool', 'Enterprise_GiftCardAccount_Model_Resource_Pool');
    }
}
