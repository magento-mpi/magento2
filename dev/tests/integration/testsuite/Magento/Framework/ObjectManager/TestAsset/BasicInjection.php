<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\TestAsset;

class BasicInjection
{
    /**
     * @var \Magento\Framework\ObjectManager\TestAsset\Basic
     */
    protected $_object;

    /**
     * @param \Magento\Framework\ObjectManager\TestAsset\Basic $object
     */
    public function __construct(\Magento\Framework\ObjectManager\TestAsset\Basic $object)
    {
        $this->_object = $object;
    }
}
