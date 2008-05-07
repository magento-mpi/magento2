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
 * Webservices default server handler
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Server_Handler extends Mage_Api_Model_Server_Handler_Abstract
{
    /**
     * Start web service session
     *
     * @return string
     */
    public function startSession()
    {
        $this->_startSession();
        return $this->_getSession()->getSessionId();
    }


    /**
     * End web service session
     *
     * @param string $sessionId
     * @return boolean
     */
    public function endSession($sessionId)
    {
        $this->_startSession($sessionId);
        $this->_getSession()->clear();
        return true;
    }

    /**
     * Login user and retrive session id
     *
     * @param string $username
     * @param string $apiKey
     * @return string
     */
    public function login($username, $apiKey)
    {
        $this->_startSession();
        try {
            $this->_getSession()->login($username, $apiKey);
        } catch (Mage_Core_Exception $e) {
            return $this->_fault(Mage_Api_Model_Server::FAULT_LOGIN, $e->getMessage());
        } catch (Exception $e) {
            return $this->_fault(Mage_Api_Model_Server::FAULT_LOGIN, Mage::helper('api')->__('Unable to login.'));

        }
        return $this->_getSession()->getSessionId();
    }

    /**
     * Call registered api functionality
     *
     * @param string $apiPath
     * @param string $sessionId
     * @param array $args
     * @return mixed
     */
    public function call($apiPath, $sessionId, $args)
    {
        $this->_startSession($sessionId);

        list($resourceName, $methodName) = explode('.', $apiPath);

        if (empty($resourceName) || empty($methodName)) {
            return $this->_fault(Mage_Api_Model_Server::FAULT_CALL, Mage::helper('api')->__('Invalid api path.'));
        }

        if (!isset($this->_getConfig()->getResources()->$resourceName)
            || !isset($this->_getConfig()->getResources()->$resourceName->methods->$methodName)) {
            return $this->_fault(Mage_Api_Model_Server::FAULT_CALL, Mage::helper('api')->__('Invalid api path.'));
        }

        if (isset($this->_getConfig()->getResources()->$resourceName->acl)
            && !$this->_isAllowed((string)$this->_getConfig()->getResources()->$resourceName->acl)) {
            return $this->_fault(Mage_Api_Model_Server::FAULT_PERMISSIONS, Mage::helper('api')->__('Access denied.'));

        }


        if (isset($this->_getConfig()->getResources()->$resourceName->methods->$methodName->acl)
            && !$this->_isAllowed((string)$this->_getConfig()->getResources()->$resourceName->methods->$methodName->acl)) {
            return $this->_fault(Mage_Api_Model_Server::FAULT_PERMISSIONS, Mage::helper('api')->__('Access denied.'));
        }

        $methodInfo = $this->_getConfig()->getResources()->$resourceName->methods->$methodName;

        try {
            $method = (isset($methodInfo->method) ? (string) $methodInfo->method : $methodName);

            $modelName = (string) $this->_getConfig()->getResources()->$resourceName->model;
            try {
                $model = Mage::getModel($modelName);
            } catch (Exception $e) {
                Mage::throwException(Mage::helper('api')->__('Api path is not callable.'));
            }

            if (is_callable(array(&$model, $method))) {
                if (isset($methodInfo->arguments) && ((string)$methodInfo->arguments) == 'array') {
                    return $model->$method($args);
                } else {
                    return call_user_func_array(array(&$model, $method), $args);
                }
            } else {
                Mage::throwException(Mage::helper('api')->__('Api path is not callable.'));
            }
        } catch (Mage_Core_Exception $e) {
            return $this->_fault(Mage_Api_Model_Server::FAULT_CALL, $e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            return $this->_fault(Mage_Api_Model_Server::FAULT_CALL, Mage::helper('api')->__('Internal Error. Please see log for details.'));
        }
    }
} // Class Mage_Api_Model_Server_Handler End