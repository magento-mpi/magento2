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

class Magento_ObjectManager_TestAsset_ConstructorSixArguments
    extends Magento_ObjectManager_TestAsset_ConstructorFiveArguments
{
    /**
     * @var Magento_ObjectManager_TestAsset_Basic
     */
    protected $_six;

    /**
     * Six arguments
     *
     * @param Magento_ObjectManager_TestAsset_Basic $one
     * @param Magento_ObjectManager_TestAsset_Basic $two
     * @param Magento_ObjectManager_TestAsset_Basic $three
     * @param Magento_ObjectManager_TestAsset_Basic $four
     * @param Magento_ObjectManager_TestAsset_Basic $five
     * @param Magento_ObjectManager_TestAsset_Basic $six
     */
    public function __construct(
        Magento_ObjectManager_TestAsset_Basic $one,
        Magento_ObjectManager_TestAsset_Basic $two,
        Magento_ObjectManager_TestAsset_Basic $three,
        Magento_ObjectManager_TestAsset_Basic $four,
        Magento_ObjectManager_TestAsset_Basic $five,
        Magento_ObjectManager_TestAsset_Basic $six
    ) {
        parent::__construct($one, $two, $three, $four, $five);
        $this->_six = $six;
    }
}
