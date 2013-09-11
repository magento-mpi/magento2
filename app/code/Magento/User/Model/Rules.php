<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Rules Model
 *
 * @method \Magento\User\Model\Resource\Rules _getResource()
 * @method \Magento\User\Model\Resource\Rules getResource()
 * @method int getRoleId()
 * @method \Magento\User\Model\Rules setRoleId(int $value)
 * @method string getResourceId()
 * @method \Magento\User\Model\Rules setResourceId(string $value)
 * @method string getPrivileges()
 * @method \Magento\User\Model\Rules setPrivileges(string $value)
 * @method int getAssertId()
 * @method \Magento\User\Model\Rules setAssertId(int $value)
 * @method string getRoleType()
 * @method \Magento\User\Model\Rules setRoleType(string $value)
 * @method string getPermission()
 * @method \Magento\User\Model\Rules setPermission(string $value)
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Model;

class Rules extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('\Magento\User\Model\Resource\Rules');
    }

    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    public function getCollection()
    {
        return \Mage::getResourceModel('\Magento\User\Model\Resource\Permissions\Collection');
    }

    public function saveRel()
    {
        $this->getResource()->saveRel($this);
        return $this;
    }
}
