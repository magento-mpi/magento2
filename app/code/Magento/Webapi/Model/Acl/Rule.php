<?php
/**
 * Web API ACL Rules.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method int getRoleId() getRoleId()
 * @method \Magento\Webapi\Model\Acl\Rule setRoleId() setRoleId(int $value)
 * @method string getResourceId() getResourceId()
 * @method \Magento\Webapi\Model\Resource\Acl\Rule getResource() getResource()
 * @method \Magento\Webapi\Model\Resource\Acl\Rule\Collection getCollection() getCollection()
 * @method \Magento\Webapi\Model\Acl\Rule setResourceId() setResourceId(string $value)
 * @method \Magento\Webapi\Model\Acl\Rule setResources() setResources(array $resources)
 * @method array getResources() getResources()
 */
namespace Magento\Webapi\Model\Acl;

class Rule extends \Magento\Core\Model\AbstractModel
{
    /**
     * Web API ACL resource separator.
     */
    const RESOURCE_SEPARATOR = '/';

    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_init('Magento\Webapi\Model\Resource\Acl\Rule');
    }

    /**
     * Save role resources.
     *
     * @return \Magento\Webapi\Model\Acl\Rule
     */
    public function saveResources()
    {
        $this->getResource()->saveResources($this);
        return $this;
    }
}
