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
     * Get instance of inline translate config
     *
     * @return \Magento\Translate\Inline\ConfigInterface
     */
    public function get()
    {
        return $this->_objectManager->get('Magento\Translate\Inline\ConfigInterface');
    }
}
