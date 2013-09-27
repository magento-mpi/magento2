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

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Adminhtml\Block\System\Store;

class DeleteTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHeaderText()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\System\Store\Delete */
        $block = $layout->createBlock('Magento\Adminhtml\Block\System\Store\Delete', 'block');

        $dataObject = new \Magento\Object;
        $form = $block->getChildBlock('form');
        $form->setDataObject($dataObject);

        $expectedValue = 'header_text_test';
        $this->assertNotContains($expectedValue, (string)$block->getHeaderText());

        $dataObject->setName($expectedValue);
        $this->assertContains($expectedValue, (string)$block->getHeaderText());
    }
}
