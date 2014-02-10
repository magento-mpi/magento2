<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UIMap Atomic Elements collection class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_ElementsCollection extends ArrayObject
{
    /**
     * Type of an element
     * @var string
     */
    protected $_type = '';

    /**
     * Parameters helper instance
     *
     * @var Mage_Selenium_Helper_Params
     */
    protected $_params = null;

    /**
     * Construct Uimap_ElementsCollection
     *
     * @param string $type Type of element
     * @param array $objects Elements array
     * @param Mage_Selenium_Helper_Params|null $paramsDecorator Parameters decorator instance (by default = null)
     */
    public function __construct($type, $objects, $paramsDecorator = null)
    {
        $this->_type = $type;
        $this->_params = $paramsDecorator;

        parent::__construct($objects);
    }

    /**
     * Get element type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Assign parameters decorator
     *
     * @param Mage_Selenium_Helper_Params $params Parameters decorator
     *
     * @return void
     */
    public function assignParams($params)
    {
        $this->_params = $params;
    }

    /**
     * Get element by ID
     *
     * @param string $elementId Element ID
     * @param Mage_Selenium_Helper_Params|null $paramsDecorator Parameters decorator instance (by default = null)
     *
     * @return string|null
     */
    public function get($elementId, $paramsDecorator = null)
    {
        $val = null;
        if (isset($this[$elementId])) {
            $val = $this[$elementId];

            if (!$paramsDecorator && $this->_params) {
                $paramsDecorator = $this->_params;
            }
            if ($paramsDecorator != null) {
                $val = $paramsDecorator->replaceParameters($val);
            }
        }
        return $val;
    }

    /**
     * Get ElementsCollectionIterator object
     *
     * @return \ArrayIterator|\Mage_Selenium_Uimap_ElementsCollectionIterator|\Traversable
     */
    public function getIterator()
    {
        return new Mage_Selenium_Uimap_ElementsCollectionIterator($this, $this->_params);
    }

    /**
     * Return current array entry by name
     *
     * @param $name string
     *
     * @return mixed The current array entry.
     */
    public function __get($name)
    {
        $val = $this[$name];
        if ($val && $this->_params != null) {
            $val = $this->_params->replaceParameters($val);
        }
        return $val;
    }
}

/**
 * This iterator allows to unset and modify values and keys while iterating
 * over Arrays and Objects.
 */
class Mage_Selenium_Uimap_ElementsCollectionIterator extends ArrayIterator
{
    /**
     * Parameters helper instance
     *
     * @var Mage_Selenium_Helper_Params
     */
    protected $_params = null;

    /**
     * Construct an ElementsCollectionIterator
     *
     * @param Mage_Selenium_Uimap_ElementsCollection $collection The array or object to be iterated on.
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator array (by default = null)
     *
     * @return Mage_Selenium_Uimap_ElementsCollectionIterator
     */
    public function __construct($collection, $paramsDecorator = null)
    {
        $this->_params = $paramsDecorator;
        parent::__construct($collection);
    }

    /**
     * Return current array entry
     *
     * @return mixed The current array entry.
     */
    public function current()
    {
        $val = parent::current();
        if ($val && $this->_params != null) {
            $val = $this->_params->replaceParameters($val);
        }
        return $val;
    }
}
