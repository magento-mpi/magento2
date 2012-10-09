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

class Magento_Di_TestAsset_InterfaceInjection
{
    /**
     * @var Magento_Di_TestAsset_Interface
     */
    protected $_object;

    /**
     * @param Magento_Di_TestAsset_Interface $interface
     */
    public function __construct(Magento_Di_TestAsset_Interface $object)
    {
        $this->_object = $object;
    }
}
