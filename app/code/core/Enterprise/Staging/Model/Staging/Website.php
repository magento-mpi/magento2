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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging website model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Website extends Mage_Core_Model_Abstract
{
	const EXCEPTION_LOGIN_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_LOGIN_OR_PASSWORD = 2;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_website');
    }

    /**
     * Authenticate user for frontend view
     *
     * @param  string $login
     * @param  string $password
     * @return true
     * @throws Exception
     */
    public function authenticate($login, $password)
    {
        $this->load($login, 'master_login');
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw new Exception(Mage::helper('enterprise_staging')->__('This account is not confirmed.'), self::EXCEPTION_LOGIN_NOT_CONFIRMED);
        }
        if (!$this->validatePassword($password)) {
            throw new Exception(Mage::helper('enterprise_staging')->__('Invalid login or password.'), self::EXCEPTION_INVALID_LOGIN_OR_PASSWORD);
        }
        return true;
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Enterprise_Staging_Model_Staging
     */
    public function setMasterPassword($password)
    {
        $this->setData('master_password', $password);
        $this->setMasterPasswordHash($this->hashPassword($password));
        return $this;
    }

    /**
     * Hach customer password
     *
     * @param   string $password
     * @return  string
     */
    public function hashPassword($password, $salt=null)
    {
        return Mage::helper('core')->getHash($password, !is_null($salt) ? $salt : 2);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length=6)
    {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        if (!($hash = $this->getMasterPasswordHash())) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }

    /**
     * Encrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    /**
     * Decrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
    }

    public function getMasterWebsite()
    {
    	$masterWebsiteId = $this->getMasterWebsiteId();
    	if (!is_null($masterWebsiteId)) {
    		return Mage::app()->getWebsite($masterWebsiteId);
    	} else {
    		return false;
    	}
    }

    public function getSlaveWebsite()
    {
        $slaveWebsiteId = $this->getSlaveWebsiteId();
        if (!is_null($slaveWebsiteId)) {
            return Mage::app()->getWebsite($slaveWebsiteId);
        } else {
            return false;
        }
    }

    public function getSlaveStoreIdByMasterStoreId($storeId)
    {
    	return $this->getResource()->getSlaveStoreIdByMasterStoreId($this, $storeId);
    }
}