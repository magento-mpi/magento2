<?php
/**
 * Token builder factory.
 *
 * @copyright {copyright}
 */
namespace Magento\Integration\Model\Oauth\Token;

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
     * Create token model.
     *
     * @param array $arguments
     * @return \Magento\Integration\Model\Oauth\Token
     */
    public function create($arguments = [])
    {
        return $this->_objectManager->create('Magento\Integration\Model\Oauth\Token', $arguments);
    }
}
