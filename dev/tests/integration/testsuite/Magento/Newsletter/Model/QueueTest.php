<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Newsletter_Model_QueueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Newsletter/_files/queue.php
     * @magentoConfigFixture fixturestore_store general/locale/code de_DE
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriber()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $themes = array('frontend' => 'magento_blank');
        /** @var $design Magento_Core_Model_View_Design */
        $design = $objectManager->create('Magento_Core_Model_View_DesignInterface', array('themes' => $themes));
        $objectManager->addSharedInstance($design, 'Magento_Core_Model_View_Design');
        /** @var $appEmulation Magento_Core_Model_App_Emulation */
        $appEmulation = $objectManager->create('Magento_Core_Model_App_Emulation', array('viewDesign' => $design));
        $objectManager->addSharedInstance($appEmulation, 'Magento_Core_Model_App_Emulation');
        /** @var $app Magento_Core_Model_App */
        $app = $objectManager->get('Magento_Core_Model_App');
        $app->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();

        /** @var $collection Magento_Core_Model_Resource_Theme_Collection */
        $collection = $objectManager->create('Magento_Core_Model_Resource_Theme_Collection');
        $themeId = $collection->getThemeByFullPath('frontend/magento_demo')->getId();
        /** @var $storeManager Magento_Core_Model_StoreManagerInterface */
        $storeManager = $objectManager->get('Magento_Core_Model_StoreManagerInterface');
        $storeManager->getStore('fixturestore')->setConfig('design/theme/theme_id', $themeId);

        $subscriberOne = $this->getMock('Zend_Mail', array('send', 'setBodyHTML'), array('utf-8'));
        $subscriberOne->expects($this->any())->method('send');
        $subscriberTwo = clone $subscriberOne;
        $subscriberOne->expects($this->once())->method('setBodyHTML')->with(
            $this->stringEndsWith('/static/frontend/magento_blank/en_US/images/logo.gif')
        );
        $subscriberTwo->expects($this->once())->method('setBodyHTML')->with(
            $this->stringEndsWith('/static/frontend/magento_demo/de_DE/images/logo.gif')
        );
        /** @var $filter Magento_Newsletter_Model_Template_Filter */
        $filter = $objectManager->get('Magento_Newsletter_Model_Template_Filter');

        $emailTemplate = $this->getMock('Magento_Core_Model_Email_Template',
            array('_getMail', '_getLogoUrl', '__wakeup', 'setTemplateFilter'),
            array(
                $objectManager->get('Magento_Core_Model_Context'),
                $objectManager->get('Magento_Core_Model_Registry'),
                $appEmulation,
                $objectManager->get('Magento_Filesystem'),
                $objectManager->get('Magento_Core_Model_View_Url'),
                $objectManager->get('Magento_Core_Model_View_FileSystem'),
                $design,
                $objectManager->get('Magento_Core_Model_Store_ConfigInterface'),
                $objectManager->get('Magento_Core_Model_ConfigInterface'),
                $objectManager->get('Magento_Core_Model_Email_Template_FilterFactory'),
                $objectManager->get('Magento_Core_Model_StoreManagerInterface'),
                $objectManager->get('Magento_Core_Model_Dir'),
                $objectManager->get('Magento_Core_Model_Email_Template_Config'),
            )
        );
        $emailTemplate->expects($this->once())
            ->method('setTemplateFilter')
            ->with($filter);

        $emailTemplate->expects($this->exactly(2))->method('_getMail')->will($this->onConsecutiveCalls(
            $subscriberOne, $subscriberTwo
        ));
        /** @var $queue Magento_Newsletter_Model_Queue */
        $queue = $objectManager->create('Magento_Newsletter_Model_Queue', array(
            'filter' => $filter,
            'data'   => array('email_template' => $emailTemplate)
        ));
        $queue->load('Subject', 'newsletter_subject'); // fixture
        $queue->sendPerSubscriber();
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/queue.php
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriberProblem()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
            ->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $mail = $this->getMock('Zend_Mail', array('send'), array('utf-8'));
        $brokenMail = $this->getMock('Zend_Mail', array('send'), array('utf-8'));
        $errorMsg = md5(microtime());
        $brokenMail->expects($this->any())->method('send')->will($this->throwException(new Exception($errorMsg, 99)));
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $template = $this->getMock('Magento_Core_Model_Email_Template',
            array('_getMail', '_getLogoUrl', '__wakeup'),
            array(
                $objectManager->get('Magento_Core_Model_Context'),
                $objectManager->get('Magento_Core_Model_Registry'),
                $objectManager->get('Magento_Core_Model_App_Emulation'),
                $objectManager->get('Magento_Filesystem'),
                $objectManager->get('Magento_Core_Model_View_Url'),
                $objectManager->get('Magento_Core_Model_View_FileSystem'),
                $objectManager->get('Magento_Core_Model_View_Design'),
                $objectManager->get('Magento_Core_Model_Store_ConfigInterface'),
                $objectManager->get('Magento_Core_Model_ConfigInterface'),
                $objectManager->get('Magento_Core_Model_Email_Template_FilterFactory'),
                $objectManager->get('Magento_Core_Model_StoreManagerInterface'),
                $objectManager->get('Magento_Core_Model_Dir'),
                $objectManager->get('Magento_Core_Model_Email_Template_Config'),
            )
        );
        $template->expects($this->any())->method('_getMail')->will($this->onConsecutiveCalls($mail, $brokenMail));

        $storeConfig = $objectManager->get('Magento_Core_Model_Store_Config');
        $coreStoreConfig = new ReflectionProperty($template, '_coreStoreConfig');
        $coreStoreConfig->setAccessible(true);
        $coreStoreConfig->setValue($template, $storeConfig);

        $queue = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Newsletter_Model_Queue',
            array('data' => array('email_template' => $template))
        );
        $queue->load('Subject', 'newsletter_subject'); // fixture
        $problem = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Newsletter_Model_Problem');
        $problem->load($queue->getId(), 'queue_id');
        $this->assertEmpty($problem->getId());

        $queue->sendPerSubscriber();

        $problem->load($queue->getId(), 'queue_id');
        $this->assertNotEmpty($problem->getId());
        $this->assertEquals(99, $problem->getProblemErrorCode());
        $this->assertEquals($errorMsg, $problem->getProblemErrorText());
    }
}
