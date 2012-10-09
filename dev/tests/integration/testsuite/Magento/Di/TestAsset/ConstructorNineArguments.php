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

class Magento_Di_TestAsset_ConstructorNineArguments extends Magento_Di_TestAsset_ConstructorEightArguments
{
    /**
     * @var Magento_Di_TestAsset_Basic
     */
    protected $_nine;

    /**
     * Nine arguments
     *
     * @param Magento_Di_TestAsset_Basic $one
     * @param Magento_Di_TestAsset_Basic $two
     * @param Magento_Di_TestAsset_Basic $three
     * @param Magento_Di_TestAsset_Basic $four
     * @param Magento_Di_TestAsset_Basic $five
     * @param Magento_Di_TestAsset_Basic $six
     * @param Magento_Di_TestAsset_Basic $seven
     * @param Magento_Di_TestAsset_Basic $eight
     * @param Magento_Di_TestAsset_Basic $nine
     */
    public function __construct(
        Magento_Di_TestAsset_Basic $one,
        Magento_Di_TestAsset_Basic $two,
        Magento_Di_TestAsset_Basic $three,
        Magento_Di_TestAsset_Basic $four,
        Magento_Di_TestAsset_Basic $five,
        Magento_Di_TestAsset_Basic $six,
        Magento_Di_TestAsset_Basic $seven,
        Magento_Di_TestAsset_Basic $eight,
        Magento_Di_TestAsset_Basic $nine
    ) {
        parent::__construct($one, $two, $three, $four, $five, $six, $seven, $eight);
        $this->_nine = $nine;
    }
}
