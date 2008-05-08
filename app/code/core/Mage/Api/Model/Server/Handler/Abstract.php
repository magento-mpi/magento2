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
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice default handler
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api_Model_Server_Handler_Abstract
{
    public function __construct()
    {
        set_error_handler(array(get_class($this), 'hadlePhpError'), E_ALL);
    }

    static public function hadlePhpError($errorCode, $errorMessage, $errorFile)
    {
        Mage::log($errorMessage, null, $errorFile);
        if (in_array($errorCode, array(E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR))) {
            $this->_fault('internal');
        }
        return true;
    }


    /**
     * Retrive webservice session
     *
     * @return Mage_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('api/session');
    }

    /**
     * Retrive webservice configuration
     *
     * @return Mage_Api_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('api/config');
    }

    /**
     * Retrive webservice server
     *
     * @return Mage_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('api/server');
    }

    /**
     * Start webservice session
     *
     * @param string $sessionId
     * @return Mage_Api_Model_Server_Handler_Abstract
     */
    protected function _startSession($sessionId=null)
    {
        $this->_getSession()->setSessionId($sessionId);
        $this->_getSession()->init('api', 'api');
        return $this;
    }

    /**
     * Check current user permission on resource and privilege
     *
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  bool
     */
    protected function _isAllowed($resource, $privilege=null)
    {
        return $this->_getSession()->isAllowed($resource, $privilege);
    }

    /**
     * Dispatch webservice fault
     *
     * @param string $faultName
     * @param string $resourceName
     * @param string $customMessage
     */
    protected function _fault($faultName, $resourceName=null, $customMessage=null)
    {
        $faults = $this->_getConfig()->getFaults($resourceName);
        if (!isset($faults[$faultName])) {
            $this->_fault('unknown');
            return;
        }
        $this->_getServer()->getAdapter()->fault(
            $faults[$faultName]['code'],
            (is_null($customMessage) ? $faults[$faultName]['message'] : $customMessage)
        );
    }

    /**
     * Login user and retrive session id
     *
     * @param string $username
     * @param string $apiKey
     * @return string
     */
    abstract public function login($username, $apiKey);

    /**
     * Call registered api functionality
     *
     * @param string $apiPath
     * @param string $sessionId
     * @param array $args
     * @return mixed
     */
    abstract public function call($apiPath, $sessionId, $args);

    /**
     * Start web service session
     *
     * @return string
     */
    abstract public function startSession();

    /**
     * End web service session
     *
     * @param string $sessionId
     * @return boolean
     */
    abstract public function endSession($sessionId);

} // Class Mage_Api_Model_Server_Handler_Abstract End