<?php
/**
 * DOM document factory.
 *
 * @copyright {copyright}
 */
class Magento_DomDocument_Factory
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
     * Create empty DOM document instance.
     *
     * @return DOMDocument
     */
    public function createDomDocument()
    {
        return $this->_objectManager->create('DOMDocument', array(), false);
    }
}
