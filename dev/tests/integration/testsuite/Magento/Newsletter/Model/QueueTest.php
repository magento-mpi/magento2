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
     * @magentoConfigFixture frontend/design/theme/full_name magento_blank
     * @magentoConfigFixture fixturestore_store general/locale/code de_DE
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriber()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $themes = array('frontend' => 'magento_blank', 'adminhtml' => 'magento_backend', 'install' => 'magento_basic');
        $design = $objectManager->create('Magento_Core_Model_View_Design', array('themes' => $themes));
        $objectManager->addSharedInstance($design, 'Magento_Core_Model_View_Design');

        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Resource_Theme_Collection');
        $themeId = $collection->getThemeByFullPath('frontend/magento_demo')->getId();
        Mage::app()->getStore('fixturestore')->setConfig('design/theme/theme_id', $themeId);

        $subscriberOne = $this->getMock('Zend_Mail', array('send', 'setBodyHTML'), array('utf-8'));
        $subscriberOne->expects($this->any())->method('send');
        $subscriberTwo = clone $subscriberOne;
        $subscriberOne->expects($this->once())->method('setBodyHTML')->with(
            $this->stringEndsWith('/static/frontend/magento_blank/en_US/images/logo.gif')
        );
        $subscriberTwo->expects($this->once())->method('setBodyHTML')->with(
            $this->stringEndsWith('/static/frontend/magento_demo/de_DE/images/logo.gif')
        );

        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $emailTemplate = $this->getMock('Magento_Core_Model_Email_Template',
            array('_getMail', '_getLogoUrl', '__wakeup'),
            array(
                $objectManager->get('Magento_Core_Model_Context'),
                $objectManager->get('Magento_Core_Model_Registry'),
                $objectManager->get('Magento_Filesystem'),
                $objectManager->get('Magento_Core_Model_View_Url'),
                $objectManager->get('Magento_Core_Model_View_FileSystem'),
                $objectManager->get('Magento_Core_Model_View_Design'),
                $objectManager->get('Magento_Core_Model_Store_Config'),
                $objectManager->get('Magento_Core_Model_Config')
            )
        );

        $storeConfig = $objectManager->get('Magento_Core_Model_Store_Config');
        $coreStoreConfig = new ReflectionProperty($emailTemplate, '_coreStoreConfig');
        $coreStoreConfig->setAccessible(true);
        $coreStoreConfig->setValue($emailTemplate, $storeConfig);

        $emailTemplate->expects($this->exactly(2))->method('_getMail')->will($this->onConsecutiveCalls(
            $subscriberOne, $subscriberTwo
        ));

        $queue = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Newsletter_Model_Queue',
            array('data' => array('email_template' => $emailTemplate))
        );
        $queue->load('Subject', 'newsletter_subject'); // fixture
        $queue->sendPerSubscriber();
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/queue.php
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriberProblem()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
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
                $objectManager->get('Magento_Filesystem'),
                $objectManager->get('Magento_Core_Model_View_Url'),
                $objectManager->get('Magento_Core_Model_View_FileSystem'),
                $objectManager->get('Magento_Core_Model_View_Design'),
                $objectManager->get('Magento_Core_Model_Store_Config'),
                $objectManager->get('Magento_Core_Model_Config')
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
