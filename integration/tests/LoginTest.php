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
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 */
class LoginTest extends Magento_Test_Webservice
{
    public function testLogin()
    {
        /** @var $client Magento_Test_Webservice_SoapV1|Magento_Test_Webservice_SoapV2|Magento_Test_Webservice_XmlRpc */
        $client = $this->getWebService();
        $this->assertTrue($client->hasSession());
    }

    public function testLoginDirect()
    {
        $client = new SoapClient(TESTS_WEBSERVICE_URL.'/api/soap/?wsdl=1', array('trace'=>true));
        $sessionId = $client->login(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY);
        $this->assertNotEmpty($sessionId);
    }
}
