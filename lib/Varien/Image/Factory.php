<?php
/**
 * Image factory.
 *
 * @copyright {copyright}
 */
class Varien_Image_Factory
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
     * Create image instance.
     *
     * @param string|null $fileName
     * @param string $adapter
     * @return Varien_Image
     */
    public function create($fileName = null, $adapter = Varien_Image_Adapter::ADAPTER_GD2)
    {
        return $this->_objectManager->create('Varien_Image', array('fileName' => $fileName, 'adapter' => $adapter));
    }
}
