<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_IndexControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @covers Mage_Adminhtml_IndexController::changeLocaleAction
     */
    public function testChangeLocaleAction()
    {
        $expected = 'de_DE';
        $this->getRequest()->setParam('locale', $expected);
        $this->dispatch('admin/index/changeLocale');
        $actual = Mage::getSingleton('Mage_Backend_Model_Session')->getLocale();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Mage_Adminhtml_IndexController::globalSearchAction
     */
    public function testGlobalSearchAction()
    {
        $this->getRequest()->setParam('isAjax', 'true');
        $this->getRequest()->setPost('query', 'dummy');
        $this->dispatch('admin/index/globalSearch');

        $actual = $this->getResponse()->getBody();
        $this->assertStringEndsWith('</ul>', trim($actual));
    }
}
