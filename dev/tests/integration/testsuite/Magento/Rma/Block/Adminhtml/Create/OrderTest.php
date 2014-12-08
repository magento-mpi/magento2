<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Create;

/**
 * @magentoAppArea adminhtml
 */
class OrderTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $utility = new \Magento\Framework\View\Utility\Layout($this);
        $layoutArguments = array_merge($utility->getLayoutDependencies(), ['area' => 'adminhtml']);
        $layout = $utility->getLayoutFromFixture(
            __DIR__ . '/../../../_files/adminhtml_rma_chooseorder.xml',
            $layoutArguments
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->addSharedInstance($layout, 'Magento\TestFramework\View\Layout');

        $layout->getUpdate()->addHandle('adminhtml_rma_chooseorder')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('rma_create_order');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\DesignInterface'
        )->setArea(
            'adminhtml'
        );
        $this->assertContains('<div id="magento_rma_rma_create_order_grid">', $layout->getOutput());
    }
}
