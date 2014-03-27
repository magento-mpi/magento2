<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model\Config\Source\Cart;

use Magento\TestFramework\Helper\ObjectManager;

class SummaryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Checkout\Model\Config\Source\Cart\Summary */
    protected $object;

    /** @var ObjectManager */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->object = $this->objectManager->getObject('Magento\Checkout\Model\Config\Source\Cart\Summary');
    }

    public function testToOptionArray()
    {
        $this->assertEquals(
            [
                ['value' => 0, 'label' => 'Display number of items in cart'],
                ['value' => 1, 'label' => 'Display item quantities'],
            ],
            $this->object->toOptionArray()
        );
    }
}
