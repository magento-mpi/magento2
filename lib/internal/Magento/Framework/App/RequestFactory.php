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
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create request
     *
     * @param array $arguments
     * @return RequestInterface
     */
    public function create(array $arguments = array())
    {
        return $this->objectManager->create('Magento\Framework\App\RequestInterface', $arguments);
    }
}
