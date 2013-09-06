<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element collection
 *
 * @category    Magento
 * @package     Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Collection implements ArrayAccess, IteratorAggregate
{

    /**
     * Elements storage
     *
     * @var array
     */
    private $_elements;

    /**
     * Elements container
     *
     * @var Magento_Data_Form_Abstract
     */
    private $_container;

    /**
     * Class constructor
     *
     * @param Magento_Data_Form_Abstract $container
     */
    public function __construct($container)
    {
        $this->_elements = array();
        $this->_container = $container;
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_elements);
    }

    /**
     * Implementation of ArrayAccess:offsetSet()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->_elements[$key] = $value;
    }

    /**
     * Implementation of ArrayAccess:offsetGet()
     *
     * @param mixed $key
     */
    public function offsetGet($key)
    {
        return $this->_elements[$key];
    }

    /**
     * Implementation of ArrayAccess:offsetUnset()
     *
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        unset($this->_elements[$key]);
    }

    /**
     * Implementation of ArrayAccess:offsetExists()
     *
     * @param mixed $key
     * @return boolean
     */
    public function offsetExists($key)
    {
        return isset($this->_elements[$key]);
    }

    /**
     * Add element to collection
     *
     * @todo get it straight with $after
     * @param Magento_Data_Form_Element_Abstract $element
     * @param bool|string $after
     *
     * @return Magento_Data_Form_Element_Collection
     */
    public function add(Magento_Data_Form_Element_Abstract $element, $after = false)
    {
        // Set the Form for the node
        if ($this->_container->getForm() instanceof Magento_Data_Form) {
            $element->setContainer($this->_container);
            $element->setForm($this->_container->getForm());
        }

        if ($after === false) {
            $this->_elements[] = $element;
        }
        elseif ($after === '^') {
            array_unshift($this->_elements, $element);
        }
        elseif (is_string($after)) {
            $newOrderElements = array();
            foreach ($this->_elements as $index => $currElement) {
                if ($currElement->getId() == $after) {
                    $newOrderElements[] = $currElement;
                    $newOrderElements[] = $element;
                    $this->_elements = array_merge($newOrderElements, array_slice($this->_elements, $index + 1));
                    return $element;
                }
                $newOrderElements[] = $currElement;
            }
            $this->_elements[] = $element;
        }

        return $element;
    }

    /**
     * Sort elements by values using a user-defined comparison function
     *
     * @param mixed $callback
     * @return Magento_Data_Form_Element_Collection
     */
    public function usort($callback)
    {
        usort($this->_elements, $callback);
        return $this;
    }

    /**
     * Remove element from collection
     *
     * @param mixed $elementId
     * @return Magento_Data_Form_Element_Collection
     */
    public function remove($elementId)
    {
        foreach ($this->_elements as $index => $element) {
            if ($elementId == $element->getId()) {
                unset($this->_elements[$index]);
            }
        }
        // Renumber elements for further correct adding and removing other elements
        $this->_elements = array_merge($this->_elements, array());
        return $this;
    }

    /**
     * Count elements in collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->_elements);
    }

    /**
     * Find element by ID
     *
     * @param mixed $elementId
     * @return Magento_Data_Form_Element_Abstract|null
     */
    public function searchById($elementId)
    {
        foreach ($this->_elements as $element) {
            if ($element->getId() == $elementId) {
                return $element;
            }
        }
        return null;
    }

}
