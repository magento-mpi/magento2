<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract adminGws observer
 *
 */
namespace Magento\AdminGws\Model\Observer;

class AbstractObserver
{
    /**
     * @var \Magento\AdminGws\Model\Role
     */
    protected $_role;

    /**
     * Initialize helper
     *
     */
    public function __construct(\Magento\AdminGws\Model\Role $role)
    {
        $this->_role = $role;
    }
}
