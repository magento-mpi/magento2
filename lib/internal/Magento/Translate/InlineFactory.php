<?php
/**
 * Inline translation factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate;

class InlineFactory
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Object constructor
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return instance of inline translate parser object
     *
     * @param array $data
     * @throws \InvalidArgumentException
     * @return \Magento\Translate\InlineInterface
     */
    public function get(array $data = null)
    {
        $model = $this->_objectManager->get('Magento\Translate\InlineInterface', $data);
        return $model;
    }
}
