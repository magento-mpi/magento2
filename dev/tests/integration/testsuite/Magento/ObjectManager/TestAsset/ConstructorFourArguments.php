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

class Magento_Di_TestAsset_ConstructorFourArguments extends Magento_Di_TestAsset_ConstructorThreeArguments
{
    /**
     * @var Magento_Di_TestAsset_Basic
     */
    protected $_four;

    /**
     * Four arguments
     *
     * @param Magento_Di_TestAsset_Basic $one
     * @param Magento_Di_TestAsset_Basic $two
     * @param Magento_Di_TestAsset_Basic $three
     * @param Magento_Di_TestAsset_Basic $four
     */
    public function __construct(
        Magento_Di_TestAsset_Basic $one,
        Magento_Di_TestAsset_Basic $two,
        Magento_Di_TestAsset_Basic $three,
        Magento_Di_TestAsset_Basic $four
    ) {
        parent::__construct($one, $two, $three);
        $this->_four = $four;
    }
}
