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

class Magento_ObjectManager_TestAsset_ConstructorFiveArguments
    extends Magento_ObjectManager_TestAsset_ConstructorFourArguments
{
    /**
     * @var Magento_ObjectManager_TestAsset_Basic
     */
    protected $_five;

    /**
     * Five arguments
     *
     * @param Magento_ObjectManager_TestAsset_Basic $one
     * @param Magento_ObjectManager_TestAsset_Basic $two
     * @param Magento_ObjectManager_TestAsset_Basic $three
     * @param Magento_ObjectManager_TestAsset_Basic $four
     * @param Magento_ObjectManager_TestAsset_Basic $five
     */
    public function __construct(
        Magento_ObjectManager_TestAsset_Basic $one,
        Magento_ObjectManager_TestAsset_Basic $two,
        Magento_ObjectManager_TestAsset_Basic $three,
        Magento_ObjectManager_TestAsset_Basic $four,
        Magento_ObjectManager_TestAsset_Basic $five
    ) {
        parent::__construct($one, $two, $three, $four);
        $this->_five = $five;
    }
}
