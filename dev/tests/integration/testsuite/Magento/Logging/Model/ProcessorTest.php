<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Enterprise logging processor
 *
 * @magentoAppArea adminhtml
 */
class Magento_Logging_Model_ProcessorTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * Test that configured admin actions are properly logged
     *
     * @param string $url
     * @param string $action
     * @param array $post
     * @dataProvider adminActionDataProvider
     * @magentoDataFixture Magento/Logging/_files/user_and_role.php
     * @magentoDbIsolation enabled
     */
    public function testLoggingProcessorLogsAction($url, $action, array $post = array())
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
            ->loadArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Logging_Model_Event')->getCollection();
        $eventCountBefore = count($collection);

        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Url')
            ->turnOffSecretKey();

        $this->_auth = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Auth');
        $this->_auth->login(Magento_TestFramework_Bootstrap::ADMIN_NAME,
            Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);

        $this->getRequest()->setServer(array('REQUEST_METHOD' => 'POST'));
        $this->getRequest()->setPost(
            array_merge($post, array('form_key' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->get('Magento_Core_Model_Session')->getFormKey()))
        );
        $this->dispatch($url);
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Logging_Model_Event')->getCollection();

        // Number 2 means we have "login" event logged first and then the tested one.
        $eventCountAfter = $eventCountBefore + 2;
        $this->assertEquals($eventCountAfter, count($collection), $action . ' event wasn\'t logged');
        $lastEvent = $collection->getLastItem();
        $this->assertEquals($action, $lastEvent['action']);
    }

    /**
     * @return array
     */
    public function adminActionDataProvider()
    {
        return array(
            array('backend/admin/user/edit/user_id/2', 'view'),
            array(
                'backend/admin/user/save', 'save',
                array(
                    'firstname' => 'firstname',
                    'lastname'  => 'lastname',
                    'email' => 'newuniqueuser@ebay.com',
                    'roles[]' => 1,
                    'username' => 'newuniqueuser',
                    'password' => 'password123'
                )
            ),
            array('backend/admin/user/delete/user_id/2', 'delete'),
            array('backend/admin/user_role/editrole/rid/2', 'view'),
            array(
                'backend/admin/user_role/saverole', 'save',
                array(
                    'rolename' => 'newrole2',
                    'gws_is_all' => '1'
                )
            ),
            array('backend/admin/user_role/delete/rid/2', 'delete'),
            array('backend/admin/tax_class/ajaxDelete', 'delete', array('class_id' => 1, 'isAjax' => true)),
            array('backend/admin/tax_class/ajaxSave', 'save',
                array(
                    'class_id' => null,
                    'class_name' => 'test',
                    'class_type' => 'PRODUCT',
                    'isAjax' => true,
                )
            )
        );
    }
}
