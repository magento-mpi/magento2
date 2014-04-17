<?php
/**
 * Parser factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate\Inline;

class ParserFactory
{
    /**
     * Default instance type
     */
    const DEFAULT_INSTANCE_TYPE = 'Magento\Translate\Inline\ParserInterface';

    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Object constructor
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return instance of inline translate parser object
     *
     * @return \Magento\Translate\Inline\ParserInterface
     */
    public function get()
    {
        return $this->_objectManager->get(self::DEFAULT_INSTANCE_TYPE);
    }

    /**
     * @param array $arguments
     * @return \Magento\Translate\Inline\ParserInterface
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(self::DEFAULT_INSTANCE_TYPE, $arguments);
    }
}
