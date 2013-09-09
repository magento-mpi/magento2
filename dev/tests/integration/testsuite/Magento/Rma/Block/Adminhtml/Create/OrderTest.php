<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Rma_Block_Adminhtml_Create_OrderTest extends PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $utility = new Magento_Core_Utility_Layout($this);
        $layoutArguments = array_merge($utility->getLayoutDependencies(), array('area' => 'adminhtml'));
        $layout = $utility->getLayoutFromFixture(
            __DIR__ . '/../../../_files/chooseorder.xml',
            $layoutArguments
        );
        $layout->getUpdate()->addHandle('adminhtml_rma_chooseorder')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('rma_create_order');
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea('adminhtml');
        $this->assertContains('<div id="magento_rma_rma_create_order_grid">', $layout->getOutput());
    }
}
