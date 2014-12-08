<?php
/**
 * Nonce builder factory.
 *
 * @copyright {copyright}
 */
namespace Magento\Integration\Model\Oauth\Nonce;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create nonce model.
     *
     * @param array $arguments
     * @return \Magento\Integration\Model\Oauth\Nonce
     */
    public function create($arguments = [])
    {
        return $this->_objectManager->create('Magento\Integration\Model\Oauth\Nonce', $arguments);
    }
}
