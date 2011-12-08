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
    public function __construct()
    {
        $this->_role = Mage::getSingleton('Enterprise_AdminGws_Model_Role');
    }
}
