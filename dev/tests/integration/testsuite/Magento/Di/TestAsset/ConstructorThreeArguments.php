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

class Magento_Di_TestAsset_ConstructorThreeArguments extends Magento_Di_TestAsset_ConstructorTwoArguments
{
    /**
     * @var Magento_Di_TestAsset_Basic
     */
    protected $_three;

    /**
     * Three arguments
     *
     * @param Magento_Di_TestAsset_Basic $one
     * @param Magento_Di_TestAsset_Basic $two
     * @param Magento_Di_TestAsset_Basic $three
     */
    public function __construct(
        Magento_Di_TestAsset_Basic $one,
        Magento_Di_TestAsset_Basic $two,
        Magento_Di_TestAsset_Basic $three
    ) {
        parent::__construct($one, $two);
        $this->_three = $three;
    }
}

