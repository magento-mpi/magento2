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
class Magento_AdminGws_Model_Observer_Abstract
{
    /**
     * @var Magento_AdminGws_Model_Role
     */
    protected $_role;

    /**
     * Initialize helper
     *
     */
    public function __construct(Magento_AdminGws_Model_Role $role)
    {
        $this->_role = $role;
    }
}
