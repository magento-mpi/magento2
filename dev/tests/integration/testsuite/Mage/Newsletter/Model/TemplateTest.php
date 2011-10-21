<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Newsletter
 * @magentoDataFixture Mage/Core/_files/store.php
 */
class Mage_Newsletter_Model_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Newsletter_Model_Template
     */
    protected  $_model = null;

    protected function setUp()
    {
        $this->_model = new Mage_Newsletter_Model_Template;
    }

    public function getProcessedTemplateDataProvider()
    {
        return array(
            'install'        => array('install',   'default',      'default/default/default'),
            'backend'        => array('adminhtml', 'admin',        'default/default/default'),
            'frontend'       => array('frontend',  'default',      'default/iphone/default'),
            'frontend store' => array('frontend',  'fixturestore', 'default/default/blue'),
        );
    }

    /**
     * @magentoConfigFixture                    install/design/theme/full_name   default/default/default
     * @magentoConfigFixture                    adminhtml/design/theme/full_name default/default/default
     * @magentoConfigFixture current_store      design/theme/full_name           default/iphone/default
     * @magentoConfigFixture fixturestore_store design/theme/full_name           default/default/blue
     * @magentoAppIsolation  enabled
     * @dataProvider         getProcessedTemplateDataProvider
     */
    public function testGetProcessedTemplate($area, $store, $design)
    {
        $this->markTestIncomplete('Test partially fails bc of MAGETWO-557.');
        $this->_model->setTemplateText('{{skin url="Mage_Page::favicon.ico"}}');
        $this->assertStringEndsWith('skin/frontend/default/default/default/en_US/Mage_Page/favicon.ico',
            $this->_model->getProcessedTemplate()
        );
        $this->_model->emulateDesign($store, $area);
        $expectedTemplateText = "skin/{$area}/{$design}/en_US/Mage_Page/favicon.ico";
        $this->assertStringEndsWith($expectedTemplateText, $this->_model->getProcessedTemplate());
        $this->_model->revertDesign();
    }
}
