<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Email\Model;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Email\Model\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \Zend_Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mail;

    protected function setUp()
    {
        $this->_mail = $this->getMock(
            'Zend_Mail', array('send', 'addTo', 'addBcc', 'setReturnPath', 'setReplyTo'), array('utf-8')
        );
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $this->getMockBuilder('Magento\Email\Model\Template')
            ->setMethods(array('_getMail'))
            ->setConstructorArgs(array(
                $objectManager->get('Magento\Core\Model\Context'),
                $objectManager->get('Magento\View\DesignInterface'),
                $objectManager->get('Magento\Core\Model\Registry'),
                $objectManager->get('Magento\Core\Model\App\Emulation'),
                $objectManager->get('Magento\Core\Model\StoreManager'),
                $objectManager->create('Magento\App\Filesystem'),
                $objectManager->create('Magento\View\Url'),
                $objectManager->create('Magento\View\FileSystem'),
                $objectManager->create('Magento\Core\Model\Store\Config'),
                $objectManager->create('Magento\Core\Model\Config'),
                $objectManager->get('Magento\Email\Model\Template\FilterFactory'),
                $objectManager->get('Magento\Email\Model\Template\Config'),
            ))
            ->getMock();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');
        $this->_model->expects($this->any())->method('_getMail')->will($this->returnCallback(array($this, 'getMail')));
        $this->_model->setSenderName('sender')->setSenderEmail('sender@example.com')->setTemplateSubject('Subject');
    }

    /**
     * Return a disposable \Zend_Mail instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend_Mail
     */
    public function getMail()
    {
        return clone $this->_mail;
    }

    public function testSetGetTemplateFilter()
    {
        $filter = $this->_model->getTemplateFilter();
        $this->assertSame($filter, $this->_model->getTemplateFilter());
        $this->assertEquals(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
                ->getStore()->getId(),
            $filter->getStoreId()
        );

        $filter = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Email\Model\Template\Filter');
        $this->_model->setTemplateFilter($filter);
        $this->assertSame($filter, $this->_model->getTemplateFilter());
    }

    public function testLoadDefault()
    {
        $this->_model->loadDefault('customer_create_account_email_template');
        $this->assertNotEmpty($this->_model->getTemplateText());
        $this->assertNotEmpty($this->_model->getTemplateSubject());
        $this->assertNotEmpty($this->_model->getOrigTemplateVariables());
        $this->assertInternalType('array', \Zend_Json::decode($this->_model->getOrigTemplateVariables()));
        $this->assertNotEmpty($this->_model->getTemplateStyles());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testGetProcessedTemplate()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_setBlankThemeForFixtureStore();
        $expectedViewUrl = 'static/frontend/magento_blank/en_US/Magento_Theme/favicon.ico';
        $this->_model->setTemplateText('{{view url="Magento_Theme::favicon.ico"}}');
        $this->assertStringEndsNotWith($expectedViewUrl, $this->_model->getProcessedTemplate());
        $this->_model->setDesignConfig(array(
            'area' => 'frontend',
            'store' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Core\Model\StoreManagerInterface')->getStore('fixturestore')->getId()
        ));
        $this->assertStringEndsWith($expectedViewUrl, $this->_model->getProcessedTemplate());
    }

    /**
     * Set 'magento_blank' for the 'fixturestore' store.
     * Application isolation is required, if a test uses this method.
     */
    protected function _setBlankThemeForFixtureStore()
    {
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\View\Design\ThemeInterface');
        $theme->load('magento_blank', 'theme_path');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore('fixturestore')->setConfig(
                \Magento\View\DesignInterface::XML_PATH_THEME_ID,
                $theme->getId()
            );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/design_change.php
     */
    public function testGetProcessedTemplateDesignChange()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_model->setTemplateText('{{view url="Magento_Theme::favicon.ico"}}');
        $this->assertStringEndsWith(
            'static/frontend/magento_blank/en_US/Magento_Theme/favicon.ico',
            $this->_model->getProcessedTemplate()
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testGetProcessedTemplateSubject()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_setBlankThemeForFixtureStore();
        $expectedViewUrl = 'static/frontend/magento_blank/en_US/Magento_Theme/favicon.ico';
        $this->_model->setTemplateSubject('{{view url="Magento_Theme::favicon.ico"}}');
        $this->assertStringEndsNotWith($expectedViewUrl, $this->_model->getProcessedTemplateSubject(array()));
        $this->_model->setDesignConfig(array(
            'area' => 'frontend',
            'store' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Core\Model\StoreManagerInterface')->getStore('fixturestore')->getId()
        ));
        $this->assertStringEndsWith($expectedViewUrl, $this->_model->getProcessedTemplateSubject(array()));
    }

    /**
     * @covers \Magento\Email\Model\Template::send
     * @covers \Magento\Email\Model\Template::addBcc
     * @covers \Magento\Email\Model\Template::setReturnPath
     * @covers \Magento\Email\Model\Template::setReplyTo
     */
    public function testSend()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_mail->expects($this->exactly(2))->method('send');
        $this->_mail->expects($this->once())->method('addBcc')->with('bcc@example.com');
        $this->_mail->expects($this->once())->method('setReturnPath')->with('return@example.com');
        $this->_mail->expects($this->once())->method('setReplyTo')->with('replyto@example.com');

        $this->_model->addBcc('bcc@example.com')
            ->setReturnPath('return@example.com')
            ->setReplyTo('replyto@example.com')
        ;
        $this->assertNull($this->_model->getSendingException());
        $this->assertTrue($this->_model->send('test@example.com'));
        $this->assertNull($this->_model->getSendingException());

        // send once again to make sure bcc, return path and reply-to were not invoked second time
        $this->assertTrue($this->_model->send('test@example.com'));
    }

    public function testSendMultipleRecipients()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_mail->expects($this->at(0))->method('addTo')->with('one@example.com', '=?utf-8?B?TmFtZSBPbmU=?=');
        $this->_mail->expects($this->at(1))->method('addTo')->with('two@example.com', '=?utf-8?B?dHdv?=');
        $this->assertTrue($this->_model->send(array('one@example.com', 'two@example.com'), array('Name One')));
    }

    public function testSendFailure()
    {
        $exception = new \Exception('test');
        $this->_mail->expects($this->once())->method('send')->will($this->throwException($exception));

        $this->assertNull($this->_model->getSendingException());
        $this->assertFalse($this->_model->send('test@example.com'));
        $this->assertSame($exception, $this->_model->getSendingException());
    }

    public function testSendTransactional()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_model->sendTransactional('customer_create_account_email_template',
            array('name' => 'Sender Name', 'email' => 'sender@example.com'), 'recipient@example.com', 'Recipient Name'
        );
        $this->assertEquals('customer_create_account_email_template', $this->_model->getId());
        $this->assertTrue($this->_model->getSentSuccess());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Email template 'wrong_id' is not defined
     */
    public function testSendTransactionalWrongId()
    {
        $this->_model->sendTransactional('wrong_id',
            array('name' => 'Sender Name', 'email' => 'sender@example.com'), 'recipient@example.com', 'Recipient Name'
        );
    }
}
