<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API Role source model.
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Webapi\Model\Source\Acl;

class Role implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Role
     */
    protected $_resource = null;

    /**
     * Prepare required models.
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        if (isset($data['resource'])) {
            $this->_resource = $data['resource'];
        } else {
            $this->_resource = \Mage::getResourceModel('Magento\Webapi\Model\Resource\Acl\Role');
        }
    }

    /**
     * Retrieve option hash of Web API Roles.
     *
     * @param bool $addEmpty
     * @return array
     */
    public function toOptionHash($addEmpty = true)
    {
        $options = $this->_getResourceModel()->getRolesList();
        if ($addEmpty) {
            $options = array('' => '') + $options;
        }
        return $options;
    }

    /**
     * Get roles resource model.
     *
     * @return \Magento\Webapi\Model\Resource\Acl\Role
     */
    protected function _getResourceModel()
    {
        return $this->_resource;
    }

    /**
     * Return option array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_getResourceModel()->getRolesList();
        return $options;
    }
}
