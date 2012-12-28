<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Test API login method
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class LoginTest extends Magento_Test_TestCase_ApiAbstract
{
    /**
     * Restore session to make possible another tests work
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        // TODO: Fix fatal error
//        self::$_adapterRegistry[self::$_defaultAdapterCode]->init();
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

        $config = array('dbname' => 'fake_db', 'password' => 'fake_password', 'username' => 'fake_username');

        $adapter = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array('delete', 'quote'), array((array)$config));
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

        $userResource = $this->getMock(
            'Mage_Api_Model_Resource_User',
            array('getTable', '_getWriteAdapter', '_getReadAdapter')
        );
        $userResource->expects($this->any())->method('_getWriteAdapter')->will($this->returnValue($adapter));
        $userResource->expects($this->any())->method('_getReadAdapter')->will($this->returnValue($adapter));
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
     * Test using API with arbitrary session id
     */
    public function testUseInvalidSessionIdCategoryCreate()
    {
        $sessionId = '3e5f2c59cad5a08528461f6a9f4b727d';

        $client = $this->getWebService();

        $this->setExpectedException($client->getExceptionClass());

        $categoryFixture = require dirname(__FILE__) . '/Mage/Catalog/Category/_fixture/categoryData.php';

        $client->setSession($sessionId)
            ->call('catalogCategory', $categoryFixture['create']);
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
                'username' => 'test_user_01',
                'api_key' => '123123q',
                'is_active' => 1,
            ),
            array(
                'username' => 'test_user_02',
                'api_key' => '123123q',
                'is_active' => 1,
            ),
        );
        $roles = array(
            array(
                'name' => 'test_role_01',
                'pid' => 0,
                'role_type' => 'G',
            ),
            array(
                'name' => 'test_role_02',
                'pid' => 0,
                'role_type' => 'G',
            ),
        );
        $resource = array('all');

        $user1 = Mage::getModel('Mage_Api_Model_User');
        $role1 = Mage::getModel('Mage_Api_Model_Roles');
        $this->addModelToDelete($user1, true)
            ->addModelToDelete($role1, true);
        $relation1 = Mage::getModel('Mage_Api_Model_Rules');
        $role1->setData($roles[0])->save();
        $user1->setData($users[0])
            ->save();
        $user1->setRoleIds(array($role1->getId()))
            ->saveRelations();
        $relation1->setRoleId($role1->getId())
            ->setResources($resource)
            ->saveRel();

        $user2 = Mage::getModel('Mage_Api_Model_User');
        $role2 = Mage::getModel('Mage_Api_Model_Roles');
        $this->addModelToDelete($user2, true)
            ->addModelToDelete($role2, true);
        $relation2 = Mage::getModel('Mage_Api_Model_Rules');
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
