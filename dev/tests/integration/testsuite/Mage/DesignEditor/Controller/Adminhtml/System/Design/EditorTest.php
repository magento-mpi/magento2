<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_DesignEditor_Controller_Adminhtml_System_Design_EditorTest extends Mage_Backend_Utility_Controller
{
    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_dataHelper;

    protected function setUp()
    {
        parent::setUp();
        $this->_dataHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
    }

    public function testIndexAction()
    {
        $this->dispatch('backend/admin/system_design_editor/index');
        $content = $this->getResponse()->getBody();

        $this->assertContains('<div class="infinite_scroll">', $content);
        $this->assertContains("jQuery('.infinite_scroll').infinite_scroll", $content);
    }

    public function testLaunchActionSingleStoreWrongThemeId()
    {
        $wrongThemeId = 999;
        $this->getRequest()->setParam('theme_id', $wrongThemeId);
        $this->dispatch('backend/admin/system_design_editor/launch');
        $this->assertSessionMessages($this->equalTo(
            array('We can\'t find theme "' . $wrongThemeId . '".')),
            Mage_Core_Model_Message::ERROR
        );
        $expected = 'http://localhost/index.php/backend/admin/system_design_editor/index/';
        $this->assertRedirect($this->stringStartsWith($expected));
    }
}
