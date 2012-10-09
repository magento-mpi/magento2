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

class Magento_Di_TestAsset_ConstructorEightArguments extends Magento_Di_TestAsset_ConstructorSevenArguments
{
    /**
     * @var Magento_Di_TestAsset_Basic
     */
    protected $_eight;

    /**
     * Eight arguments
     *
     * @param Magento_Di_TestAsset_Basic $one
     * @param Magento_Di_TestAsset_Basic $two
     * @param Magento_Di_TestAsset_Basic $three
     * @param Magento_Di_TestAsset_Basic $four
     * @param Magento_Di_TestAsset_Basic $five
     * @param Magento_Di_TestAsset_Basic $six
     * @param Magento_Di_TestAsset_Basic $seven
     * @param Magento_Di_TestAsset_Basic $eight
     */
    public function __construct(
        Magento_Di_TestAsset_Basic $one,
        Magento_Di_TestAsset_Basic $two,
        Magento_Di_TestAsset_Basic $three,
        Magento_Di_TestAsset_Basic $four,
        Magento_Di_TestAsset_Basic $five,
        Magento_Di_TestAsset_Basic $six,
        Magento_Di_TestAsset_Basic $seven,
        Magento_Di_TestAsset_Basic $eight
    ) {
        parent::__construct($one, $two, $three, $four, $five, $six, $seven);
        $this->_eight = $eight;
    }
}
