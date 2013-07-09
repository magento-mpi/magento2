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
     * @param Magento_Code_Reader_ClassReader $classReader
     */
    public function __construct(Magento_Code_Reader_ClassReader $classReader = null)
    {
        $this->_classReader = $classReader ?: new Magento_Code_Reader_ClassReader();
    }

    /**
     * Retrieve list of parents
     *
     * @param string $type
     * @return array
     */
    public function getParents($type)
    {
        return $this->_classReader->getParents($type) ?: array();
    }
}
