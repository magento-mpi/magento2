<?php
/**
 * Inline Translation config factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate\Inline;

class ConfigFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create instance of inline translate config
     *
     * @return \Magento\Translate\Inline\ConfigInterface
     */
    public function create()
    {
        return $this->_objectManager->create('Magento\Translate\Inline\ConfigInterface');
    }
}
