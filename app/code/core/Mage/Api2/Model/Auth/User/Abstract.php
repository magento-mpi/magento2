<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 User Abstract Class
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api2_Model_Auth_User_Abstract
{
    /**
     * User Role
     *
     * @var string
     */
    protected $_role;

    /**
     * Retrieve user role
     *
     * @return string
     * @throws Exception
     */
    public function getRole()
    {
        if (!$this->_role) {
            throw new Exception('User role is not set');
        }
        return $this->_role;
    }

    /**
     * Set user role
     *
     * @param string $role
     * @return Mage_Api2_Model_Auth_User_Abstract
     * @throws Exception
     */
    public function setRole($role)
    {
        if ($this->_role) {
            throw new Exception('User role has been already set');
        }
        $this->_role = $role;

        return $this;
    }
}
