<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\TestAsset;

class InterfaceInjection
{
    /**
     * @var \Magento\Framework\ObjectManager\TestAsset\TestAssetInterface
     */
    protected $_object;

    /**
     * @param \Magento\Framework\ObjectManager\TestAsset\TestAssetInterface $object
     */
    public function __construct(\Magento\Framework\ObjectManager\TestAsset\TestAssetInterface $object)
    {
        $this->_object = $object;
    }
}
