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

class Magento_ObjectManager_TestAsset_ConstructorTwoArguments
    extends Magento_ObjectManager_TestAsset_ConstructorOneArgument
{
    /**
     * @var Magento_ObjectManager_TestAsset_Basic
     */
    protected $_two;

    /**
     * Two arguments
     *
     * @param Magento_ObjectManager_TestAsset_Basic $one
     * @param Magento_ObjectManager_TestAsset_Basic $two
     */
    public function __construct(
        Magento_ObjectManager_TestAsset_Basic $one,
        Magento_ObjectManager_TestAsset_Basic $two
    ) {
        parent::__construct($one);
        $this->_two = $two;
    }
}
