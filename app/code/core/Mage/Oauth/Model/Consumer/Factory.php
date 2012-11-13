<?php
/**
 * Consumer builder factory.
 *
 * @copyright {copyright}
 */
class Mage_Oauth_Model_Consumer_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create consumer model.
     *
     * @param array $arguments
     * @return Mage_Oauth_Model_Consumer
     */
    public function create($arguments = array())
    {
        return $this->_objectManager->create('Mage_Oauth_Model_Consumer', $arguments);
    }
}
