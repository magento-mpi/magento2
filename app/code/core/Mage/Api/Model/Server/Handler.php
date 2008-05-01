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
        $this->_startSession($sessionId);
        $this->_getSession()->login($username, $apiKey);
        return $this->_getSession()->getId();
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
        // implement
    }
} // Class Mage_Api_Model_Server_Handler End