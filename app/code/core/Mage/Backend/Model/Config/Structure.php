<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration structure
 */
class Mage_Backend_Model_Config_Structure
{
    /**
     * Key that contains field type in structure array
     */
    const TYPE_KEY = '_elementType';

    /**
     * Configuration structure represented as tree
     *
     * @var array
     */
    protected $_data;

    /**
     * Config tab iterator
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    protected $_tabIterator;

    /**
     * Pool of config element flyweight objects
     *
     * @var Mage_Backend_Model_Config_Structure_Element_FlyweightPool
     */
    protected $_flyweightPool;

    /**
     * @param Mage_Backend_Model_Config_Structure_Reader $structureReader
     * @param Mage_Backend_Model_Config_Structure_Element_Iterator $tabIterator
     * @param Mage_Backend_Model_Config_Structure_Element_FlyweightPool $flyweightPool
     */
    public function __construct(
        Mage_Backend_Model_Config_Structure_Reader $structureReader,
        Mage_Backend_Model_Config_Structure_Element_Iterator $tabIterator,
        Mage_Backend_Model_Config_Structure_Element_FlyweightPool $flyweightPool
    ) {
        $this->_data = $structureReader->getData();
        $this->_tabIterator = $tabIterator;
        $this->_flyweightPool = $flyweightPool;
    }

    /**
     * Retrieve tab iterator
     *
     * @param string $websiteCode
     * @param string $storeCode
     * @return Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    public function getTabs($websiteCode, $storeCode)
    {
        foreach ($this->_data['sections'] as $sectionId => $section) {
            if (isset($section['tab']) && $section['tab']) {
                $this->_data['tabs'][$section['tab']]['children'][$sectionId] = $section;
            }
        }
        $this->_tabIterator->setElements($this->_data['tabs']);
        return $this->_tabIterator;
    }

    /**
     * Find element by path
     *
     * @param string $path
     * @return Mage_Backend_Model_Config_Structure_ElementInterface|null
     */
    public function getElement($path)
    {
        return $this->getElementByPathParts(explode('/', $path));
    }

    /**
     * Find element by path parts
     *
     * @param array $pathParts
     * @return Mage_Backend_Model_Config_Structure_ElementInterface|null
     */
    public function getElementByPathParts(array $pathParts)
    {
        $children = $this->_data['sections'];
        $child = array();
        foreach ($pathParts as $id) {
            if (array_key_exists($id, $children)) {
                $child = $children[$id];
                $children = array_key_exists('children', $child) ? $child['children'] : array();
            } else {
                return null;
            }
        }
        return $this->_flyweightPool->getFlyweight($child);
    }
}
