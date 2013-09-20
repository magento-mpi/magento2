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

class ConstructorSixArguments
    extends \Magento\ObjectManager\TestAsset\ConstructorFiveArguments
{
    /**
     * @var \Magento\ObjectManager\TestAsset\Basic
     */
    protected $_six;

    /**
     * Six arguments
     *
     * @param \Magento\ObjectManager\TestAsset\Basic $one
     * @param \Magento\ObjectManager\TestAsset\Basic $two
     * @param \Magento\ObjectManager\TestAsset\Basic $three
     * @param \Magento\ObjectManager\TestAsset\Basic $four
     * @param \Magento\ObjectManager\TestAsset\Basic $five
     * @param \Magento\ObjectManager\TestAsset\Basic $six
     */
    public function __construct(
        \Magento\ObjectManager\TestAsset\Basic $one,
        \Magento\ObjectManager\TestAsset\Basic $two,
        \Magento\ObjectManager\TestAsset\Basic $three,
        \Magento\ObjectManager\TestAsset\Basic $four,
        \Magento\ObjectManager\TestAsset\Basic $five,
        \Magento\ObjectManager\TestAsset\Basic $six
    ) {
        parent::__construct($one, $two, $three, $four, $five);
        $this->_six = $six;
    }
}
