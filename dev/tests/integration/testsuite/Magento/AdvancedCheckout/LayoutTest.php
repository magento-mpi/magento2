<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppArea frontend
     */
    public function testCartLayout()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\DesignInterface'
        )->setDesignTheme(
            'magento_plushe'
        );
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        $layout->getUpdate()->addHandle('checkout_cart_index');
        $layout->getUpdate()->load();
        $this->assertNotEmpty($layout->getUpdate()->asSimplexml()->xpath('//block[@name="sku.failed.products"]'));
    }
}
