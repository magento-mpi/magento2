<?php
/**
 * Consumer builder factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Oauth_Model_Consumer', $arguments);
    }
}
