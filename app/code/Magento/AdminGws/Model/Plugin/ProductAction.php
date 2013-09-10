<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminGws_Model_Plugin_ProductAction
{
    /**
     * @var Magento_AdminGws_Model_Role
     */
    protected $_role;

    /**
     * @param Magento_AdminGws_Model_Role $role
     */
    public function __construct(Magento_AdminGws_Model_Role $role)
    {
        $this->_role = $role;
    }

    /**
     * Check website access before adding/removing products to/from websites during mass update
     *
     * @param array $methodArguments
     * @return array
     * @throws Magento_Core_Exception
     */
    public function beforeUpdateWebsites(array $methodArguments)
    {
        if ($this->_role->getIsAll()) {
            return $methodArguments;
        }
        list(, $websiteIds, $type) = $methodArguments;
        if (in_array($type, array('remove', 'add'))) {
            if (!$this->_role->getIsWebsiteLevel()) {
                throw new Magento_Core_Exception(__('You need more permissions to save this item.'));
            }
            if (!$this->_role->hasWebsiteAccess($websiteIds, true)) {
                throw new Magento_Core_Exception(__('You need more permissions to save this item.'));
            }
        }
        return $methodArguments;
    }

}
