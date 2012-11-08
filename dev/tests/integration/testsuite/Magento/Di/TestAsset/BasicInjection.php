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

class Magento_Di_TestAsset_BasicInjection
{
    /**
     * @var Magento_Di_TestAsset_Basic
     */
    protected $_object;

    /**
     * @param Magento_Di_TestAsset_Basic $object
     */
    public function __construct(Magento_Di_TestAsset_Basic $object)
    {
        $this->_object = $object;
    }
}
