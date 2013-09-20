<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\ObjectManager
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\TestAsset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class ConstructorEightArguments
    extends \Magento\ObjectManager\TestAsset\ConstructorSevenArguments
{
    /**
     * @var \Magento\ObjectManager\TestAsset\Basic
     */
    protected $_eight;

    /**
     * Eight arguments
     *
     * @param \Magento\ObjectManager\TestAsset\Basic $one
     * @param \Magento\ObjectManager\TestAsset\Basic $two
     * @param \Magento\ObjectManager\TestAsset\Basic $three
     * @param \Magento\ObjectManager\TestAsset\Basic $four
     * @param \Magento\ObjectManager\TestAsset\Basic $five
     * @param \Magento\ObjectManager\TestAsset\Basic $six
     * @param \Magento\ObjectManager\TestAsset\Basic $seven
     * @param \Magento\ObjectManager\TestAsset\Basic $eight
     */
    public function __construct(
        \Magento\ObjectManager\TestAsset\Basic $one,
        \Magento\ObjectManager\TestAsset\Basic $two,
        \Magento\ObjectManager\TestAsset\Basic $three,
        \Magento\ObjectManager\TestAsset\Basic $four,
        \Magento\ObjectManager\TestAsset\Basic $five,
        \Magento\ObjectManager\TestAsset\Basic $six,
        \Magento\ObjectManager\TestAsset\Basic $seven,
        \Magento\ObjectManager\TestAsset\Basic $eight
    ) {
        parent::__construct($one, $two, $three, $four, $five, $six, $seven);
        $this->_eight = $eight;
    }
}
