<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Saas_PrintedTemplate_Controller_Adminhtml_TemplateTest extends Mage_Backend_Utility_Controller
{
    public function testPrintedTemplateIsInstalled()
    {
        $this->markTestIncomplete('MAGETWO-7075');
        $this->dispatch('backend/admin/template/index');

        $this->assertInstanceOf(
            'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid',
            Mage::app()->getLayout()->getBlock('printed.template.grid'),
            'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid block is not loaded'
        );

        $result = $this->getResponse()->getBody();
        $expected = 'Please make sure that popups are allowed.';
        $this->assertContains(
            $expected,
            $result,
            'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid block is not rendered'
        );
    }
}
