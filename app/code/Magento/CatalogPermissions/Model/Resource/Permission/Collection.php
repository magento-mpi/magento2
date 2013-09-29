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
namespace Magento\CatalogPermissions\Model\Resource\Permission;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\CatalogPermissions\Model\Permission',
            'Magento\CatalogPermissions\Model\Resource\Permission'
        );
    }
}
