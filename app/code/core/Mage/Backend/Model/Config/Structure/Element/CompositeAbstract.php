<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Mage_Backend_Model_Config_Structure_Element_CompositeAbstract
    extends Mage_Backend_Model_Config_Structure_ElementAbstract
    implements IteratorAggregate
{
    /**
     * The name of children array identifier in data array
     *
     * @var string
     */
    protected $_childrenKey;

    /**
     * Child elements iterator
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    protected $_childrenIterator;

    /**
     * Retrieve element iterator
     *
     * @return Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    public function getIterator()
    {
        $this->_childrenIterator->setElements($this->_data[$this->_childrenKey]);
        return $this->_childrenIterator;
    }

    /**
     * Retrieve element by path
     *
     * @param string $path
     * @return Mage_Backend_Model_Config_Structure_ElementInterface
     */
    public function getElement($path)
    {
        $pathParts = explode('/', $path);
        return $this->getElement($pathParts);
    }

    /**
     * Retrieve element by path parts
     *
     * @param array $pathParts
     * @return Mage_Backend_Model_Config_Structure_ElementInterface
     */
    public function getElementByPathParts(array $pathParts)
    {
        $key = array_shift($pathParts);
        /** @var $child Mage_Backend_Model_Config_Structure_Element_CompositeAbstract */
        $child = isset($this->_data[$this->_childrenKey][$key]) ? $this->_data[$this->_childrenKey][$key] : null;
        return (count($pathParts) && $child) ? $child->getElementByPathParts($pathParts) : $child;
    }

    /**
     * Check whether element has visible child elements
     *
     * @return bool
     */
    public function hasChildren()
    {
        $result = false;
        foreach ($this->getIterator() as $child) {
            if ($child->isVisible()) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
