<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Relations_Runtime implements Magento_ObjectManager_Relations
{
    /**
     * @var Magento_Code_Reader_ClassReader
     */
    protected $_classReader;

    /**
     * Default behavior
     *
     * @var array
     */
    protected $_default = array();

    /**
     * @param Magento_Code_Reader_ClassReader $classReader
     */
    public function __construct(Magento_Code_Reader_ClassReader $classReader = null)
    {
        $this->_classReader = $classReader ?: new Magento_Code_Reader_ClassReader();
    }

    /**
     * Check whether requested type is available for read
     *
     * @param string $type
     * @return bool
     */
    public function has($type)
    {
        return class_exists($type) || interface_exists($type);
    }

    /**
     * Retrieve list of parents
     *
     * @param string $type
     * @return array
     */
    public function getParents($type)
    {
        if (!class_exists($type)) {
            return $this->_default;
        }
        return $this->_classReader->getParents($type) ?: $this->_default;
    }
}
