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

class ConstructorTwoArguments
    extends \Magento\ObjectManager\TestAsset\ConstructorOneArgument
{
    /**
     * @var \Magento\ObjectManager\TestAsset\Basic
     */
    protected $_two;

    /**
     * Two arguments
     *
     * @param \Magento\ObjectManager\TestAsset\Basic $one
     * @param \Magento\ObjectManager\TestAsset\Basic $two
     */
    public function __construct(
        \Magento\ObjectManager\TestAsset\Basic $one,
        \Magento\ObjectManager\TestAsset\Basic $two
    ) {
        parent::__construct($one);
        $this->_two = $two;
    }
}
