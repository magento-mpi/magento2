<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Iterator implements Iterator
{
    /**
     * List of element data
     *
     * @var Magento_Backend_Model_Config_Structure_ElementInterface[]
     */
    protected $_elements;

    /**
     * Config structure element flyweight
     *
     * @var Magento_Backend_Model_Config_Structure_ElementAbstract
     */
    protected $_flyweight;

    /**
     * Configuration scope
     *
     * @var string
     */
    protected $_scope;

    /**
     * Last element id
     *
     * @var string
     */
    protected $_lastId;

    /**
     * @param Magento_Backend_Model_Config_Structure_ElementAbstract $element
     */
    public function __construct(Magento_Backend_Model_Config_Structure_ElementAbstract $element)
    {
        $this->_flyweight = $element;
    }

    /**
     * Set element data
     *
     * @param array $elements
     * @param string $scope
     */
    public function setElements(array $elements, $scope)
    {
        $this->_elements = $elements;
        $this->_scope = $scope;
        if (count($elements)) {
            $lastElement = end($elements);
            $this->_lastId = $lastElement['id'];
        }
    }

    /**
     * Return the current element
     *
     * @return Magento_Backend_Model_Config_Structure_ElementInterface
     */
    public function current()
    {
        return $this->_flyweight;
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->_elements);
        if (current($this->_elements)) {
            $this->_initFlyweight(current($this->_elements));
            if (!$this->current()->isVisible()) {
                $this->next();
            }
        }
    }

    /**
     * Initialize current flyweight
     *
     * @param array $element
     */
    protected function _initFlyweight(array $element)
    {
        $this->_flyweight->setData($element, $this->_scope);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        key($this->_elements);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return (bool) current($this->_elements);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->_elements);
        if (current($this->_elements)) {
            $this->_initFlyweight(current($this->_elements));
            if (!$this->current()->isVisible()) {
                $this->next();
            }
        }
    }

    /**
     * Check whether element is last in list
     *
     * @param Magento_Backend_Model_Config_Structure_ElementInterface $element
     * @return bool
     */
    public function isLast(Magento_Backend_Model_Config_Structure_ElementInterface $element)
    {
        return $element->getId() == $this->_lastId;
    }
}
