<?php
/**
 * Consumer builder factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Oauth\Consumer;

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
     * Create consumer model.
     *
     * @param array $data
     * @return \Magento\Integration\Model\Oauth\Consumer
     */
    public function create(array $data = array())
    {
        $consumer = $this->_objectManager->create('Magento\Integration\Model\Oauth\Consumer', array());
        $consumer->setData($data);
        return $consumer;
    }
}
