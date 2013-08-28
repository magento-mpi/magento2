<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Permission collection
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CatalogPermissions_Model_Resource_Permission_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Enterprise_CatalogPermissions_Model_Permission',
            'Enterprise_CatalogPermissions_Model_Resource_Permission'
        );
    }
}
