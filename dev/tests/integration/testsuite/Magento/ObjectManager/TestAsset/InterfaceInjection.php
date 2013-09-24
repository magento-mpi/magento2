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

namespace Magento\ObjectManager\TestAsset;

class InterfaceInjection
{
    /**
     * @var \Magento\ObjectManager\TestAsset\TestAssetInterface
     */
    protected $_object;

    /**
     * @param \Magento\ObjectManager\TestAsset\TestAssetInterface $object
     */
    public function __construct(\Magento\ObjectManager\TestAsset\TestAssetInterface $object)
    {
        $this->_object = $object;
    }
}
