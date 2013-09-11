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
 * Permission resource model
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogPermissions\Model\Resource;

class Permission extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('magento_catalogpermissions', 'permission_id');
    }

    /**
     * Initialize unique scope for permission
     *
     */
    protected function _initUniqueFields()
    {
        parent::_initUniqueFields();
        $this->_uniqueFields[] = array(
            'field' => array('category_id', 'website_id', 'customer_group_id'),
            'title' => __('Permission with the same scope')
        );
    }
}
