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
class Magento_Rma_Block_Adminhtml_Edit_ItemsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Rma/_files/rma.php
     */
    public function testToHtml()
    {
        $rma = Mage::getModel('Magento_Rma_Model_Rma');
        $rma->load(1, 'increment_id');
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_rma', $rma);
        $utility = new Magento_Core_Utility_Layout($this);
        $layoutArguments = array_merge($utility->getLayoutDependencies(), array('area' => 'adminhtml'));
        $layout = $utility->getLayoutFromFixture(
            __DIR__ . '/../../../_files/adminhtml_rma_edit.xml',
            $layoutArguments
        );
        $layout->getUpdate()->addHandle('adminhtml_rma_edit')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('magento_rma_edit_tab_items');
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea('adminhtml');
        $this->assertContains('<div id="magento_rma_item_edit_grid">', $layout->getOutput());
    }
}
