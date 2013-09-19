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

class Magento_ObjectManager_TestAsset_ConstructorOneArgument
{
    /**
     * @var Magento_ObjectManager_TestAsset_Basic
     */
    protected $_one;

    /**
     * One argument
     */

    /**
     * One argument
     *
     * @param Magento_ObjectManager_TestAsset_Basic $one
     */
    public function __construct(
        Magento_ObjectManager_TestAsset_Basic $one
    ) {
        $this->_one = $one;
    }
}
