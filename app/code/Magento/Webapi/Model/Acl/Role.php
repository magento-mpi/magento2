<?php
/**
 * Role item model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl;

class Role extends \Magento\Core\Model\AbstractModel
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'webapi_role';

    /**
     * Initialize resource.
     */
    protected function _construct()
    {
        $this->_init('Magento\Webapi\Model\Resource\Acl\Role');
    }
}
