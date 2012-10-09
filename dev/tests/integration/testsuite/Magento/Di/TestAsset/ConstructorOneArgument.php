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

class Magento_Di_TestAsset_ConstructorOneArgument
{
    /**
     * @var Magento_Di_TestAsset_Basic
     */
    protected $_one;

    /**
     * One argument
     */

    /**
     * One argument
     *
     * @param Magento_Di_TestAsset_Basic $one
     */
    public function __construct(
        Magento_Di_TestAsset_Basic $one
    ) {
        $this->_one = $one;
    }
}

