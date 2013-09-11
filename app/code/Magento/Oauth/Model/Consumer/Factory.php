<?php
/**
 * Consumer builder factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Oauth\Model\Consumer;

class Factory
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
     * Create consumer model.
     *
     * @param array $arguments
     * @return \Magento\Oauth\Model\Consumer
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Oauth\Model\Consumer', $arguments);
    }
}
