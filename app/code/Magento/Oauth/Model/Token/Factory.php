<?php
/**
 * Token builder factory.
 *
 * @copyright {copyright}
 */
class Magento_Oauth_Model_Token_Factory
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
     * Create token model.
     *
     * @param array $arguments
     * @return Magento_Oauth_Model_Token
     */
    public function create($arguments = array())
    {
        return $this->_objectManager->create('Magento_Oauth_Model_Token', $arguments);
    }
}
