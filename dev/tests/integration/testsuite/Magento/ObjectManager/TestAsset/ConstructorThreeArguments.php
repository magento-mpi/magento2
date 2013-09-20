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

class ConstructorThreeArguments
    extends \Magento\ObjectManager\TestAsset\ConstructorTwoArguments
{
    /**
     * @var \Magento\ObjectManager\TestAsset\Basic
     */
    protected $_three;

    /**
     * Three arguments
     *
     * @param \Magento\ObjectManager\TestAsset\Basic $one
     * @param \Magento\ObjectManager\TestAsset\Basic $two
     * @param \Magento\ObjectManager\TestAsset\Basic $three
     */
    public function __construct(
        \Magento\ObjectManager\TestAsset\Basic $one,
        \Magento\ObjectManager\TestAsset\Basic $two,
        \Magento\ObjectManager\TestAsset\Basic $three
    ) {
        parent::__construct($one, $two);
        $this->_three = $three;
    }
}
