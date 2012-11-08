<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_TestAsset_ConstructorTwoArguments extends Magento_Di_TestAsset_ConstructorOneArgument
{
    /**
     * @var Magento_Di_TestAsset_Basic
     */
    protected $_two;

    /**
     * Two arguments
     *
     * @param Magento_Di_TestAsset_Basic $one
     * @param Magento_Di_TestAsset_Basic $two
     */
    public function __construct(
        Magento_Di_TestAsset_Basic $one,
        Magento_Di_TestAsset_Basic $two
    ) {
        parent::__construct($one);
        $this->_two = $two;
    }
}

