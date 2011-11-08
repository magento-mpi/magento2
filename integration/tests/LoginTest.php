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
    /**
     * Test API login
     *
     * @return void
     */
    public function _testLogin()
    {
        /** @var $client Magento_Test_Webservice_SoapV1|Magento_Test_Webservice_SoapV2|Magento_Test_Webservice_XmlRpc */
        $client = $this->getWebService();
        $this->assertTrue($client->hasSession());
    }

    /**
     * Test API login through SoapClient (soap v1.1)
     *
     * @return void
     */
    public function _testLoginDirect()
    {
        $client = new SoapClient(TESTS_WEBSERVICE_URL.'/api/soap/?wsdl=1', array('trace'=>true, 'exceptions'=>false));
        $sessionId = $client->login(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY);
        $this->assertNotEmpty($sessionId);
    }

    /**
     * Test that Client Session Timeout param was properly quoted
     *
     * @magentoConfigFixture current_store api/config/session_timeout 3600)); DROP TABLE bbb;
     */
    public function testLoginCleanOldSessionsTimeoutSqlInjection()
    {
        $user = $this->getMock('Mage_Api_Model_User', array('getId'));
        $user->expects($this->any())->method('getId')->will($this->returnValue('fake_user'));

        $userResource = $this->getMock('Mage_Api_Model_Mysql4_User', array('getTable'));
        $userResource->expects($this->any())->method('getTable')->will($this->returnValue('fake_table'));
        try {
            $userResource->cleanOldSessions($user);
        } catch (Exception $e) {
            $trace = $e->getTrace();
            $query = $trace[3]['args'][0];

            $this->assertStringMatchesFormat('%s3600%s', $query);
            $this->assertStringNotMatchesFormat('%sDROP TABLE bbb%s', $query);
        }
    }
}
