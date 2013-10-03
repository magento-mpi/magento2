<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\TestAsset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class ConstructorNineArguments
    extends \Magento\ObjectManager\TestAsset\ConstructorEightArguments
{
    /**
     * @var \Magento\ObjectManager\TestAsset\Basic
     */
    protected $_nine;

    /**
     * Nine arguments
     *
     * @param \Magento\ObjectManager\TestAsset\Basic $one
     * @param \Magento\ObjectManager\TestAsset\Basic $two
     * @param \Magento\ObjectManager\TestAsset\Basic $three
     * @param \Magento\ObjectManager\TestAsset\Basic $four
     * @param \Magento\ObjectManager\TestAsset\Basic $five
     * @param \Magento\ObjectManager\TestAsset\Basic $six
     * @param \Magento\ObjectManager\TestAsset\Basic $seven
     * @param \Magento\ObjectManager\TestAsset\Basic $eight
     * @param \Magento\ObjectManager\TestAsset\Basic $nine
     */
    public function __construct(
        \Magento\ObjectManager\TestAsset\Basic $one,
        \Magento\ObjectManager\TestAsset\Basic $two,
        \Magento\ObjectManager\TestAsset\Basic $three,
        \Magento\ObjectManager\TestAsset\Basic $four,
        \Magento\ObjectManager\TestAsset\Basic $five,
        \Magento\ObjectManager\TestAsset\Basic $six,
        \Magento\ObjectManager\TestAsset\Basic $seven,
        \Magento\ObjectManager\TestAsset\Basic $eight,
        \Magento\ObjectManager\TestAsset\Basic $nine
    ) {
        parent::__construct($one, $two, $three, $four, $five, $six, $seven, $eight);
        $this->_nine = $nine;
    }
}
