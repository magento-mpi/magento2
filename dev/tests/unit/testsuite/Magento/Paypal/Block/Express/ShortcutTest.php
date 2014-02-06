<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Block\Express;

class ShortcutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Block\Express\Shortcut|PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $helper->getObject('Magento\Paypal\Block\Express\Shortcut');
    }

    public function testGetAlias()
    {
        $this->assertEquals('product.info.addtocart.paypal', $this->model->getAlias());
    }
}
