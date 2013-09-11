<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method \Magento\Api\Model\Resource\Rules _getResource()
 * @method \Magento\Api\Model\Resource\Rules getResource()
 * @method int getRoleId()
 * @method \Magento\Api\Model\Rules setRoleId(int $value)
 * @method string getResourceId()
 * @method \Magento\Api\Model\Rules setResourceId(string $value)
 * @method string getPrivileges()
 * @method \Magento\Api\Model\Rules setPrivileges(string $value)
 * @method int getAssertId()
 * @method \Magento\Api\Model\Rules setAssertId(int $value)
 * @method string getRoleType()
 * @method \Magento\Api\Model\Rules setRoleType(string $value)
 * @method string getPermission()
 * @method \Magento\Api\Model\Rules setPermission(string $value)
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model;

class Rules extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('\Magento\Api\Model\Resource\Rules');
    }

    public function update() {
        $this->getResource()->update($this);
        return $this;
    }

    public function getCollection() {
        return \Mage::getResourceModel('Magento\Api\Model\Resource\Permissions\Collection');
    }

    public function saveRel() {
        $this->getResource()->saveRel($this);
        return $this;
    }
}
