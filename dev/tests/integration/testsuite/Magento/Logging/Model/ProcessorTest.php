<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model;

/**
 * Test Enterprise logging processor
 *
 * @magentoAppArea adminhtml
 */
class ProcessorTest extends \Magento\TestFramework\TestCase\AbstractController
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
    public function testLoggingProcessorLogsAction($url, $action, array $post = [])
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Logging\Model\Event')->getCollection();
        $eventCountBefore = count($collection);

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Backend\Model\UrlInterface'
        )->turnOffSecretKey();

        $this->_auth = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Auth');
        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );

        $this->getRequest()->setServer(['REQUEST_METHOD' => 'POST']);
        $this->getRequest()->setPost(
            array_merge(
                $post,
                [
                    'form_key' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                        'Magento\Framework\Data\Form\FormKey'
                    )->getFormKey()
                ]
            )
        );
        $this->dispatch($url);
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Logging\Model\Event'
        )->getCollection();

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
        return [
            ['backend/admin/user/edit/user_id/2', 'view'],
            [
                'backend/admin/user/save',
                'save',
                [
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'email' => 'newuniqueuser@ebay.com',
                    'roles[]' => 1,
                    'username' => 'newuniqueuser',
                    'password' => 'password123'
                ]
            ],
            ['backend/admin/user/delete/user_id/2', 'delete'],
            ['backend/admin/user_role/editrole/rid/2', 'view'],
            ['backend/admin/user_role/saverole', 'save', ['rolename' => 'newrole2', 'gws_is_all' => '1']],
            ['backend/admin/user_role/delete/rid/2', 'delete'],
            ['backend/tax/tax/ajaxDelete', 'delete', ['class_id' => 2, 'isAjax' => true]],
            [
                'backend/tax/tax/ajaxSave',
                'save',
                ['class_id' => null, 'class_name' => 'test', 'class_type' => 'PRODUCT', 'isAjax' => true]
            ]
        ];
    }
}
