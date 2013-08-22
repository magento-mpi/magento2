<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Permission collection
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogPermissions_Model_Resource_Permission_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento_CatalogPermissions_Model_Permission',
            'Magento_CatalogPermissions_Model_Resource_Permission'
        );
    }
}
