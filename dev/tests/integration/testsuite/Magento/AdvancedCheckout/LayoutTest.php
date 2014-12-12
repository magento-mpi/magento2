<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
            'Magento\Framework\View\DesignInterface'
        )->setDesignTheme(
            'Magento/luma'
        );
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        $layout->getUpdate()->addHandle('checkout_cart_index');
        $layout->getUpdate()->load();
        $this->assertNotEmpty($layout->getUpdate()->asSimplexml()->xpath('//block[@name="sku.failed.products"]'));
    }
}
