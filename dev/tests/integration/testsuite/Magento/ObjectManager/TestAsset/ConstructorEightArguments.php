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

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_ObjectManager_TestAsset_ConstructorEightArguments
    extends Magento_ObjectManager_TestAsset_ConstructorSevenArguments
{
    /**
     * @var Magento_ObjectManager_TestAsset_Basic
     */
    protected $_eight;

    /**
     * Eight arguments
     *
     * @param Magento_ObjectManager_TestAsset_Basic $one
     * @param Magento_ObjectManager_TestAsset_Basic $two
     * @param Magento_ObjectManager_TestAsset_Basic $three
     * @param Magento_ObjectManager_TestAsset_Basic $four
     * @param Magento_ObjectManager_TestAsset_Basic $five
     * @param Magento_ObjectManager_TestAsset_Basic $six
     * @param Magento_ObjectManager_TestAsset_Basic $seven
     * @param Magento_ObjectManager_TestAsset_Basic $eight
     */
    public function __construct(
        Magento_ObjectManager_TestAsset_Basic $one,
        Magento_ObjectManager_TestAsset_Basic $two,
        Magento_ObjectManager_TestAsset_Basic $three,
        Magento_ObjectManager_TestAsset_Basic $four,
        Magento_ObjectManager_TestAsset_Basic $five,
        Magento_ObjectManager_TestAsset_Basic $six,
        Magento_ObjectManager_TestAsset_Basic $seven,
        Magento_ObjectManager_TestAsset_Basic $eight
    ) {
        parent::__construct($one, $two, $three, $four, $five, $six, $seven);
        $this->_eight = $eight;
    }
}
