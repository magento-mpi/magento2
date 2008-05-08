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
        } catch (Exception $e) {
            return $this->_fault('access_denied');
        }
        return $this->_getSession()->getSessionId();
    }

    /**
     * Call resource functionality
     *
     * @param string $resourcePath
     * @param string $sessionId
     * @param array  $args
     * @return mixed
     */
    public function call($apiPath, $sessionId, $args)
    {
        $this->_startSession($sessionId);

        list($resourceName, $methodName) = explode('.', $apiPath);

        if (empty($resourceName) || empty($methodName)) {
            return $this->_fault('resource_path_invalid');
        }

        if (!isset($this->_getConfig()->getResources()->$resourceName)
            || !isset($this->_getConfig()->getResources()->$resourceName->methods->$methodName)) {
            return $this->_fault('resource_path_invalid');
        }

        if (isset($this->_getConfig()->getResources()->$resourceName->acl)
            && !$this->_isAllowed((string)$this->_getConfig()->getResources()->$resourceName->acl)) {
            return $this->_fault('access_denied');

        }


        if (isset($this->_getConfig()->getResources()->$resourceName->methods->$methodName->acl)
            && !$this->_isAllowed((string)$this->_getConfig()->getResources()->$resourceName->methods->$methodName->acl)) {
            return $this->_fault('access_denied');
        }

        $methodInfo = $this->_getConfig()->getResources()->$resourceName->methods->$methodName;

        try {
            $method = (isset($methodInfo->method) ? (string) $methodInfo->method : $methodName);

            $modelName = (string) $this->_getConfig()->getResources()->$resourceName->model;
            try {
                $model = Mage::getModel($modelName);
            } catch (Exception $e) {
                throw new Mage_Api_Exception('resource_path_not_callable');
            }

            if (is_callable(array(&$model, $method))) {
                if (isset($methodInfo->arguments) && ((string)$methodInfo->arguments) == 'array') {
                    return $model->$method($args);
                } else {
                    return call_user_func_array(array(&$model, $method), $args);
                }
            } else {
                throw new Mage_Api_Exception('resource_path_not_callable');
            }
        } catch (Mage_Api_Exception $e) {
            return $this->_fault($e->getMessage(), $resourceName, $e->getCustomMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            return $this->_fault('internal');
        }
    }

    /**
     * List of available resources
     *
     * @param string $sessionId
     * @return array
     */
    public function resources($sessionId)
    {
        $this->_startSession($sessionId);
        $resources = array();
        foreach ($this->_getConfig()->getResources() as $resourceName => $resource) {
            if (isset($resource->acl) && !$this->_isAllowed((string) $resource->acl)) {
                continue;
            }

            $methods = array();
            foreach ($resource->methods->children() as $methodName => $method) {
                if (isset($method->acl) && !$this->_isAllowed((string) $method->acl)) {
                    continue;
                }
                $methods[] = array(
                    'title' => (string) $method->title,
                    'path'  => $resourceName . '.' . $methodName
                );
            }

            if (count($methods) == 0) {
                continue;
            }

            $resources[] = array(
                'title'   => (string) $resource->title,
                'name'    => $resourceName,
                'methods' => $methods
            );
        }

        return $resources;
    }

    /**
     * List of resource faults
     *
     * @param string $resourceName
     * @return array
     */
    public function resourceFaults($resourceName, $sessionId)
    {
        $this->_startSession($sessionId);
        if (empty($resourceName)
            || !isset($this->_getConfig()->getResources()->$resourceName)) {
            return $this->_fault('resource_path_invalid');
        }

        if (isset($this->_getConfig()->getResources()->$resourceName->acl)
            && !$this->_isAllowed((string)$this->_getConfig()->getResources()->$resourceName->acl)) {
            return $this->_fault('access_denied');
        }

        return array_values($this->_getConfig()->getFaults($resourceName));
    }

    /**
     * List of global faults
     *
     * @param  string $sessionId
     * @return array
     */
    public function globalFaults($sessionId)
    {
        $this->_startSession($sessionId);
        return array_values($this->_getConfig()->getFaults());
    }
} // Class Mage_Api_Model_Server_Handler End