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
     * @return \Magento\Translate\Inline\ParserInterface
     */
    public function get()
    {
        return $this->_objectManager->get('Magento\Translate\Inline\ParserInterface');
    }

    /**
     * @param array $data
     * @return \Magento\Translate\Inline\ParserInterface
     */
    public function create(array $data = null)
    {
        return $this->_objectManager->create('Magento\Translate\Inline\ParserInterface', $data);
    }
}
