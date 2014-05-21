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
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create nonce model.
     *
     * @param array $arguments
     * @return \Magento\Integration\Model\Oauth\Nonce
     */
    public function create($arguments = array())
    {
        return $this->_objectManager->create('Magento\Integration\Model\Oauth\Nonce', $arguments);
    }
}
