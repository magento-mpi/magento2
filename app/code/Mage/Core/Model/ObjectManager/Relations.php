<?php
/**
 * List of parent classes with their parents and interfaces
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_ObjectManager_Relations implements Magento_ObjectManager_Relations
{
    /**
     * Relations file
     *
     * @var string
     */
    protected $_filePath;

    /**
     * List of class relations
     *
     * @var array
     */
    protected $_relations;

    /**
     * @param Mage_Core_Model_Dir $dirs
     */
    public function __construct(Mage_Core_Model_Dir $dirs)
    {
        $this->_filePath = $dirs->getDir(Mage_Core_Model_Dir::DI) . DIRECTORY_SEPARATOR . 'relations.php';
    }

    /**
     * Serialize relations data
     *
     * @return array
     */
    public function __sleep()
    {
        return array();
    }

    /**
     * Retrieve parents for class
     *
     * @param string $type
     * @return array
     */
    public function getParents($type)
    {
        if (!$this->_relations) {
            $this->_relations = unserialize(file_get_contents($this->_filePath));
        }
        return isset($this->_relations[$type]) ? $this->_relations[$type] : array();
    }
}
