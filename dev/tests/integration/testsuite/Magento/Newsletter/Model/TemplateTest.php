<?php
/**
 * {license_notice}
 *
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
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Newsletter\Model\Template'
        );
    }

    /**
     * This test expects next themes for areas:
     * current_store design/theme/full_name Magento/blank
     * fixturestore_store design/theme/full_name Magento/plushe
     *
     * @magentoAppIsolation  enabled
     * @magentoAppArea adminhtml
     * @dataProvider getProcessedTemplateFrontendDataProvider
     */
    public function testGetProcessedTemplateFrontend($store, $design)
    {
        $this->_model->setTemplateText('{{view url="Magento_Theme::favicon.ico"}}');
        if ($store != 'default') {
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Framework\App\Config\MutableScopeConfigInterface'
            )->setValue(
                \Magento\Core\Model\View\Design::XML_PATH_THEME_ID,
                $design,
                'store',
                $store
            );
        }
        $this->_model->emulateDesign($store, 'frontend');
        $processedTemplate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\State'
        )->emulateAreaCode(
            'frontend',
            array($this->_model, 'getProcessedTemplate')
        );
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
            'frontend' => array('default', 'Magento/blank'),
            'frontend store' => array('fixturestore', 'Magento/plushe')
        );
    }

    /**
     * This test expects next themes for areas:
     * adminhtml/design/theme/full_name Magento/backend
     *
     * @magentoAppIsolation  enabled
     * @dataProvider getProcessedTemplateAreaDataProvider
     */
    public function testGetProcessedTemplateArea($area, $design)
    {
        $this->_model->setTemplateText('{{view url="Magento_Theme::favicon.ico"}}');
        $this->_model->emulateDesign('default', $area);
        $processedTemplate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\State'
        )->emulateAreaCode(
            $area,
            array($this->_model, 'getProcessedTemplate')
        );
        $expectedTemplateText = "{$area}/{$design}/en_US/Magento_Theme/favicon.ico";
        $this->assertStringEndsWith($expectedTemplateText, $processedTemplate);
    }

    /**
     * @return array
     */
    public function getProcessedTemplateAreaDataProvider()
    {
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\View\Design'
        );
        return array(
            'backend' => array('adminhtml', 'Magento/backend')
        );
    }

    /**
     * @magentoConfigFixture current_store system/smtp/disable 0
     * @magentoAppIsolation enabled
     * @dataProvider isValidToSendDataProvider
     */
    public function testIsValidToSend($senderEmail, $senderName, $subject, $isValid)
    {
        $this->_model->setTemplateSenderEmail(
            $senderEmail
        )->setTemplateSenderName(
            $senderName
        )->setTemplateSubject(
            $subject
        );
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
            array('', '', '', false)
        );
    }
}
