<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract adminGws observer
 *
 */
class Enterprise_AdminGws_Model_Observer_Abstract
{
    /**
     * @var Enterprise_AdminGws_Model_Role
     */
    protected $_role;

    /**
     * Initialize helper
     *
     */
    public function __construct(array $data = array())
    {
        $this->_role = isset($data['role']) ? $data['role'] : Mage::getSingleton('Enterprise_AdminGws_Model_Role');
    }
}
