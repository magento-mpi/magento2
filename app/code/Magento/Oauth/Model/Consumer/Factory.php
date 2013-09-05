<?php
/**
 * Consumer builder factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Oauth_Model_Consumer_Factory
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
     * @return Magento_Oauth_Model_Consumer
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Oauth_Model_Consumer', $arguments);
    }
}
