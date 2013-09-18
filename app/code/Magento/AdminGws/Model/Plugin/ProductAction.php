<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model\Plugin;

class ProductAction
{
    /**
     * @var \Magento\AdminGws\Model\Role
     */
    protected $_role;

    /**
     * @param \Magento\AdminGws\Model\Role $role
     */
    public function __construct(\Magento\AdminGws\Model\Role $role)
    {
        $this->_role = $role;
    }

    /**
     * Check website access before adding/removing products to/from websites during mass update
     *
     * @param array $methodArguments
     * @return array
     * @throws \Magento\Core\Exception
     */
    public function beforeUpdateWebsites(array $methodArguments)
    {
        if ($this->_role->getIsAll()) {
            return $methodArguments;
        }
        list(, $websiteIds, $type) = $methodArguments;
        if (in_array($type, array('remove', 'add'))) {
            if (!$this->_role->getIsWebsiteLevel()) {
                throw new \Magento\Core\Exception(__('You need more permissions to save this item.'));
            }
            if (!$this->_role->hasWebsiteAccess($websiteIds, true)) {
                throw new \Magento\Core\Exception(__('You need more permissions to save this item.'));
            }
        }
        return $methodArguments;
    }

}
