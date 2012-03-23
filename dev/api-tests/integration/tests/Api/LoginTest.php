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
 * Test API login method
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api_LoginTest extends Magento_Test_Webservice
{
    /**
     * Restore session to make possible another tests work
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::$_ws->init();
    }

    /**
     * Test API login
     *
     * @return void
     */
    public function testLoginSuccess()
    {
        $this->assertTrue($this->getWebService()->hasSession());
    }

    /**
     * Test that Client Session Timeout param was properly quoted
     *
     * @magentoConfigFixture current_store api/config/session_timeout 3600)); DROP TABLE bbb;
     */
    public function testLoginCleanOldSessionsTimeoutSqlInjection()
    {
        $table = 'fake_table';

        $user = $this->getMock('Mage_Api_Model_User', array('getId'));
        $user->expects($this->any())->method('getId')->will($this->returnValue('fake_user'));

        $config = array('dbname'=>'fake_db', 'password'=>'fake_password', 'username'=>'fake_username');

        $adapter = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array('delete'), array((array)$config));
        $adapter
            ->expects($this->any())
            ->method('delete')
            ->with(
                $this->equalTo($table),
                new Magento_Test_Constraint_Array(0,
                    $this->logicalAnd(
                        $this->matches('%s3600%s'),
                        $this->logicalNot($this->matches('%sDROP TABLE bbb%s'))
                    )
                )
        );

        $userResource = $this->getMock('Mage_Api_Model_Mysql4_User', array('getTable', '_getWriteAdapter'));
        $userResource->expects($this->any())->method('_getWriteAdapter')->will($this->returnValue($adapter));
        $userResource->expects($this->any())->method('getTable')->will($this->returnValue('fake_table'));

        $userResource->cleanOldSessions($user);
    }

    /**
     * Test login with invalid credentials should throw exception
     *
     */
    public function testLoginInvalidCredentials()
    {
        $client = $this->getWebService();
        $client->setSession(null);
        $this->setExpectedException($client->getExceptionClass());
        $client->login(TESTS_WEBSERVICE_USER, 'invalid_api_key');
    }

    /**
     * Test login with invalid request xml structure.
     * Open issue APIA-17, when fixed test will be passed.
     *
     */
    public function testLoginInvalidXmlStructure()
    {
        if (TESTS_WEBSERVICE_TYPE != self::TYPE_SOAPV1) {
            return;
        }

        $requestXml = file_get_contents(dirname(__FILE__) . '/../_files/requestInvalidStructure.xml');
        $location = TESTS_WEBSERVICE_URL . '/index.php/api/soap/index/';
        $action = 'urn:Mage_Api_Model_Server_HandlerAction';
        $version = 1;

        $responseXml = $this->getWebService()->getClient()->_doRequest(
            $this->getWebService()->getClient()->getSoapClient(),
            $requestXml, $location, $action, $version
        );

        $doc = new DOMDocument;
        $doc->loadXML($responseXml);
        $xpath = new DOMXpath($doc);
        $element = $xpath->query('//SOAP-ENV:Fault/faultstring')->item(0);
        $this->assertEquals('Required parameter is missing, for more details see "exception.log".',
            $element->textContent);
    }

    /**
     * Test login with custom request made without namespaces properly defined
     *
     * @return
     */
    public function testLoginInvalidXmlNamespaces()
    {
        if (TESTS_WEBSERVICE_TYPE != self::TYPE_SOAPV1) {
            return;
        }

        $requestXml = file_get_contents(dirname(__FILE__) . '/../_files/requestInvalidNamespace.xml');
        $location = TESTS_WEBSERVICE_URL . '/index.php/api/soap/index/';
        $action = 'urn:Mage_Api_Model_Server_HandlerAction';
        $version = 1;

        $responseXml = $this->getWebService()->getClient()->_doRequest(
            $this->getWebService()->getClient()->getSoapClient(),
            $requestXml, $location, $action, $version
        );

        $doc = new DOMDocument;
        $doc->loadXML($responseXml);
        $xpath = new DOMXpath($doc);
        $element = $xpath->query('//env:Fault/env:Code/env:Value')->item(0);
        $this->assertEquals('env:VersionMismatch', $element->textContent);
    }

    /**
     * Test using API with arbitrary session id
     */
    public function testUseInvalidSessionIdCategoryCreate()
    {
        $sessionId = '3e5f2c59cad5a08528461f6a9f4b727d';

        $client = $this->getWebService();

        $this->setExpectedException($client->getExceptionClass());

        $categoryFixture = require dirname(__FILE__) . '/Catalog/Category/_fixtures/categoryData.php';

        $client->setSession($sessionId)
            ->call('category.create', $categoryFixture['create']);
    }

    /**
     * Test vulnerability on session start
     */
    public function testSessionStartVulnerability()
    {
        $timeStart = time();
        $session = $this->getWebService()->login(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY);
        //try assert equals session id by old algorithm with real session id
        $time = time();
        do {
            $equal = md5($time) == $session;
            if ($equal) {
                break;
            }
            $time--;
        } while ($time >= $timeStart);

        $this->assertFalse($equal, 'Session API starting has vulnerability.');
    }

    /**
     * Check login with credentials created in specific order
     *
     * @see APIA-46
     * @return void
     */
    public function testApiUserSortingBug()
    {
        $users = array(
            array(
                'username'  => 'test_user_01',
                'api_key'   => '123123q',
                'is_active' => 1,
            ),
            array(
                'username'  => 'test_user_02',
                'api_key'   => '123123q',
                'is_active' => 1,
            ),
        );
        $roles = array(
            array(
                'name' => 'test_role_01',
                'pid'       => 0,
                'role_type' => 'G',
            ),
            array(
                'name' => 'test_role_02',
                'pid'       => 0,
                'role_type' => 'G',
            ),
        );
        $resource = array('all');

        $user1 = new Mage_Api_Model_User;
        $role1 = new Mage_Api_Model_Roles;
        $this->addModelToDelete($user1, true)
            ->addModelToDelete($role1, true);
        $relation1 = new Mage_Api_Model_Rules;
        $role1->setData($roles[0])->save();
        $user1->setData($users[0])
                ->save();
        $user1->setRoleIds(array($role1->getId()))
                ->saveRelations();
        $relation1->setRoleId($role1->getId())
                ->setResources($resource)
                ->saveRel();

        $user2 = new Mage_Api_Model_User;
        $role2 = new Mage_Api_Model_Roles;
        $this->addModelToDelete($user2, true)
            ->addModelToDelete($role2, true);
        $relation2 = new Mage_Api_Model_Rules;
        $role2->setData($roles[1])->save();
        $user2->setData($users[1])->save();
        $user2->setRoleIds(array($role2->getId()))
                ->saveRelations();

        $relation2->setRoleId($role2->getId())
                ->setResources($resource)
                ->saveRel();

        $client = $this->getWebService();
        $client->setSession(null);
        $this->assertNotEmpty(
            $client->login($users[0]['username'], $users[0]['api_key']),
            sprintf('Could not login with user "%s"', $users[0]['username'])
        );
        $this->assertNotEmpty(
            $client->login($users[1]['username'], $users[1]['api_key']),
            sprintf('Could not login with user "%s"', $users[1]['username'])
        );
    }
}
