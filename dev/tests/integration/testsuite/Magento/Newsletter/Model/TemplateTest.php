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

namespace Magento\Newsletter\Model;

/**
 * @magentoDataFixture Magento/Core/_files/store.php
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Newsletter\Model\Template
     */
    protected  $_model = null;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Newsletter\Model\Template');
    }

    /**
     * This test expects next themes for areas:
     * current_store design/theme/full_name magento_plushe
     * fixturestore_store design/theme/full_name magento_blank
     *
     * @magentoAppIsolation  enabled
     * @magentoAppArea adminhtml
     * @dataProvider getProcessedTemplateFrontendDataProvider
     */
    public function testGetProcessedTemplateFrontend($store, $design)
    {
        $this->_model->setTemplateText('{{view url="Magento_Theme::favicon.ico"}}');
        if ($store != 'default') {
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config')
                ->setValue(\Magento\Core\Model\View\Design::XML_PATH_THEME_ID, $design, 'store', $store);
        }
        $this->_model->emulateDesign($store, 'frontend');
        $processedTemplate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
            ->emulateAreaCode('frontend', array($this->_model, 'getProcessedTemplate'));
        $expectedTemplateText = "frontend/{$design}/en_US/Magento_Theme/favicon.ico";
        $this->assertStringEndsWith($expectedTemplateText, $processedTemplate);
        $this->_model->revertDesign();
    }

    /**
     * @return array
     */
    public function getProcessedTemplateFrontendDataProvider()
    {
        return array(
            'frontend'       => array('default',      'magento_plushe'),
            'frontend store' => array('fixturestore', 'magento_blank')
        );
    }

    /**
     * This test expects next themes for areas:
     * install/design/theme/full_name   magento_basic
     * adminhtml/design/theme/full_name magento_backend
     *
     * @magentoAppIsolation  enabled
     * @dataProvider getProcessedTemplateAreaDataProvider
     */
    public function testGetProcessedTemplateArea($area, $design)
    {
        $this->_model->setTemplateText('{{view url="Magento_Theme::favicon.ico"}}');
        $this->_model->emulateDesign('default', $area);
        $processedTemplate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
            ->emulateAreaCode($area, array($this->_model, 'getProcessedTemplate'));
        $expectedTemplateText = "{$area}/{$design}/en_US/Magento_Theme/favicon.ico";
        $this->assertStringEndsWith($expectedTemplateText, $processedTemplate);
    }

    /**
     * @return array
     */
    public function getProcessedTemplateAreaDataProvider()
    {
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\View\Design');
        return array(
            'install' => array('install',   $design->getConfigurationDesignTheme('install')),
            'backend' => array('adminhtml', 'magento_backend')
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
