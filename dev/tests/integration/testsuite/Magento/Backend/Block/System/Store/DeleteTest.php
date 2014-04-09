<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Store;

/**
 * @magentoAppArea adminhtml
 */
class DeleteTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHeaderText()
    {
        /** @var $layout \Magento\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Backend\Block\System\Store\Delete */
        $block = $layout->createBlock('Magento\Backend\Block\System\Store\Delete', 'block');

        $dataObject = new \Magento\Object();
        $form = $block->getChildBlock('form');
        $form->setDataObject($dataObject);

        $expectedValue = 'header_text_test';
        $this->assertNotContains($expectedValue, (string)$block->getHeaderText());

        $dataObject->setName($expectedValue);
        $this->assertContains($expectedValue, (string)$block->getHeaderText());
    }
}
