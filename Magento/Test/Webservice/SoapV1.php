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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Magento_Test_Webservice_SoapV1
{
    /** @var string */
    private $_session = null;

    /** @var Zend_Soap_Client */
    private $_client = null;

    public function init()
    {
        $this->_client = new Zend_Soap_Client(TESTS_WEBSERVICE_URL.'/api/soap/?wsdl=1');
        $this->_session =  $this->login(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY);
    }

    public function login($api, $key)
    {
        return $this->_client->login($api, $key);
    }


    public function call($path, $params = array())
    {
        return $this->_client->call($this->_session, $path, $params);
    }
}
