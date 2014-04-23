<?php
/**
 * Application request factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class RequestFactory
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
     * Create request
     *
     * @param array $arguments
     * @return RequestInterface
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Framework\App\RequestInterface', $arguments);
    }
}
