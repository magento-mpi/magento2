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
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param array $productIds
     * @param array $websiteIds
     * @param string $type
     *
     * @return void
     * @throws \Magento\Core\Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeUpdateWebsites(
        \Magento\Catalog\Model\Product\Action $subject,
        $productIds,
        $websiteIds,
        $type
    ) {
        if (!$this->_role->getIsAll()) {
            if (in_array($type, array('remove', 'add'))) {
                if (!$this->_role->getIsWebsiteLevel()) {
                    throw new \Magento\Core\Exception(__('You need more permissions to save this item.'));
                }
                if (!$this->_role->hasWebsiteAccess($websiteIds, true)) {
                    throw new \Magento\Core\Exception(__('You need more permissions to save this item.'));
                }
            }
        }
    }
}
