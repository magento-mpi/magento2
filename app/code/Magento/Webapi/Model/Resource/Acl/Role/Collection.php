<?php
/**
 * Web API Role Resource Collection.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Resource\Acl\Role;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization.
     */
    protected function _construct()
    {
        $this->_init('Magento\Webapi\Model\Acl\Role', 'Magento\Webapi\Model\Resource\Acl\Role');
    }
}
