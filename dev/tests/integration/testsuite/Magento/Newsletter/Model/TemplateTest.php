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

/**
 * @magentoDataFixture Magento/Core/_files/store.php
 */
namespace Magento\Newsletter\Model;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Newsletter\Model\Template
     */
    protected  $_model = null;

    protected function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Newsletter\Model\Template');
    }

    /**
     * This test expects next themes for areas:
     * install/design/theme/full_name   magento_basic
     * adminhtml/design/theme/full_name magento_basic
     * current_store design/theme/full_name magento_iphone
     * fixturestore_store design/theme/full_name magento_blank
     *
     * @magentoAppIsolation  enabled
     * @dataProvider         getProcessedTemplateDataProvider
     */
    public function testGetProcessedTemplate($area, $store, $design)
    {
        $this->markTestIncomplete('Test partially fails bc of MAGETWO-557.');
        $this->_model->setTemplateText('{{view url="Magento_Page::favicon.ico"}}');
        $this->assertStringEndsWith('theme/frontend/magento_demo/en_US/Magento_Page/favicon.ico',
            $this->_model->getProcessedTemplate()
        );
        $this->_model->emulateDesign($store, $area);
        $expectedTemplateText = "theme/{$area}/{$design}/en_US/Magento_Page/favicon.ico";
        $this->assertStringEndsWith($expectedTemplateText, $this->_model->getProcessedTemplate());
        $this->_model->revertDesign();
    }

    /**
     * @return array
     */
    public function getProcessedTemplateDataProvider()
    {
        return array(
            'install'        => array('install',   'default',      'magento_demo'),
            'backend'        => array('adminhtml', 'admin',        'magento_basic'),
            'frontend'       => array('frontend',  'default',      'magento_iphone'),
            'frontend store' => array('frontend',  'fixturestore', 'magento_blank'),
        );
    }

    /**
     * @magentoConfigFixture current_store system/smtp/disable 0
     * @magentoAppIsolation enabled
     * @dataProvider isValidToSendDataProvider
     */
    public function testIsValidToSend($senderEmail, $senderName, $subject, $isValid)
    {
        $this->_model->setTemplateSenderEmail($senderEmail)
            ->setTemplateSenderName($senderName)
            ->setTemplateSubject($subject);
        $this->assertSame($isValid, $this->_model->isValidForSend());
    }

    /**
     * @return array
     */
    public function isValidToSendDataProvider()
    {
        return array(
            array('john.doe@example.com', 'john.doe', 'Test Subject', true),
            array('john.doe@example.com', 'john.doe', '', false),
            array('john.doe@example.com', '', 'Test Subject', false),
            array('john.doe@example.com', '', '', false),
            array('', 'john.doe', 'Test Subject', false),
            array('', '', 'Test Subject', false),
            array('', 'john.doe', '', false),
            array('', '', '', false),
        );
    }
}
