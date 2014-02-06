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
 * Abstract UIMap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method findCheckbox()
 * @method findButton()
 * @method findDropdown()
 * @method findField()
 * @method Mage_Selenium_Uimap_Fieldset findFieldset()
 * @method findLink()
 * @method findMessage()
 * @method findMultiselect()
 * @method findPageelement()
 * @method findRadiobutton()
 * @method Mage_Selenium_Uimap_Tab findTab()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllCheckboxes()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllButtons()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllDropdowns()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllFields()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllFieldsets()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllLinks()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllMessages()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllMultiselects()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllPageelements()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllRadiobuttons()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllRequired()
 * @method Mage_Selenium_Uimap_ElementsCollection getAllTabs()
 * @method Mage_Selenium_Uimap_ElementsCollection getCheckboxes()
 * @method Mage_Selenium_Uimap_ElementsCollection getButtons()
 * @method Mage_Selenium_Uimap_ElementsCollection getDropdowns()
 * @method Mage_Selenium_Uimap_ElementsCollection getFields()
 * @method Mage_Selenium_Uimap_ElementsCollection getFieldsets()
 * @method Mage_Selenium_Uimap_ElementsCollection getLinks()
 * @method Mage_Selenium_Uimap_ElementsCollection getMessages()
 * @method Mage_Selenium_Uimap_ElementsCollection getMultiselects()
 * @method Mage_Selenium_Uimap_ElementsCollection getPageelements()
 * @method Mage_Selenium_Uimap_ElementsCollection getRadiobuttons()
 * @method Mage_Selenium_Uimap_ElementsCollection getRequired()
 * @method Mage_Selenium_Uimap_ElementsCollection getTabs()
 */
class Mage_Selenium_Uimap_Abstract
{
    /**
     * Element XPath
     * @var string
     */
    protected $_xPath = '';

    /**
     * UIMap elements
     * @var array
     */
    protected $_elements = array();

    /**
     * UIMap elements cache for recursive operations
     * @var array
     */
    protected $_elementsCache = array();

    /**
     * Parameters helper instance
     *
     * @var Mage_Selenium_Helper_Params
     */
    protected $_params = null;

    /**
     * Retrieve XPath of the current element
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return string|null
     */
    public function getXPath($paramsDecorator = null)
    {
        return $this->_applyParamsToString($this->_xPath, $paramsDecorator);
    }

    /**
     * Retrieve all elements on current level
     *
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Parser from native UIMap array to UIMap class hierarchy
     *
     * @param array &$container Array with UIMap
     *
     * @return Mage_Selenium_Uimap_Abstract
     */
    protected function _parseContainerArray(array $container)
    {
        foreach ($container as $formElemKey => &$formElemValue) {
            if (empty($formElemValue)) {
                continue;
            }
            $newElement = Mage_Selenium_Uimap_Factory::createUimapElement($formElemKey, $formElemValue);
            if (empty($newElement)) {
                continue;
            }
            if (!isset($this->_elements[$formElemKey])) {
                $this->_elements[$formElemKey] = $newElement;
            } else {
                if ($this->_elements[$formElemKey] instanceof ArrayObject) {
                    $this->_elements[$formElemKey]->append($newElement);
                }
            }
        }

        return $this;
    }

    /**
     * Assign parameters decorator to UIMap tree from any level
     *
     * @param Mage_Selenium_Helper_Params $params Parameters decorator
     *
     * @return Mage_Selenium_Uimap_Abstract
     */
    public function assignParams($params)
    {
        $this->_params = $params;
        $this->_elementsCache = null;

        foreach ($this->_elements as $elem) {
            if ($elem instanceof Mage_Selenium_Uimap_Abstract
                || $elem instanceof Mage_Selenium_Uimap_ElementsCollection
            ) {
                $elem->assignParams($params);
            } elseif ($elem instanceof ArrayObject) {
                foreach ($elem as $arrElem) {
                    if ($arrElem instanceof Mage_Selenium_Uimap_Abstract) {
                        $arrElem->assignParams($params);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get parameters decorator
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance (by default = null)
     *
     * @return Mage_Selenium_Helper_Params|null
     */
    protected function _getParams($paramsDecorator = null)
    {
        if ($paramsDecorator) {
            return $paramsDecorator;
        }

        return $this->_params;
    }

    /**
     * Apply parameters decorator to string
     *
     * @param string $text String to change
     * @param Mage_Selenium_Helper_Params|null $paramsDecorator Parameters decorator instance
     *
     * @return string
     */
    protected function _applyParamsToString($text, $paramsDecorator = null)
    {
        $paramsDecorator = $this->_getParams($paramsDecorator);
        if ($paramsDecorator) {
            return $paramsDecorator->replaceParameters($text);
        }

        return $text;
    }

    /**
     * Internal recursive function
     *
     * @param string $elementsCollection UIMap elements collection name
     * @param Mage_Selenium_Uimap_ElementsCollection|array $container UIMap container
     * @param array $cache Array with search results
     * @param Mage_Selenium_Helper_Params|null $paramsDecorator Parameters decorator instance
     *
     * @return array
     */
    protected function _getElementsRecursive($elementsCollection, &$container, &$cache, $paramsDecorator = null)
    {
        foreach ($container as $elKey => $elValue) {
            if ($elValue instanceof ArrayObject) {
                $this->_defineCache($elementsCollection, $elKey, $elValue, $cache, $paramsDecorator);
            } elseif ($elValue instanceof Mage_Selenium_Uimap_Abstract) {
                $containerUimap = $elValue->getElements();
                $this->_getElementsRecursive($elementsCollection,
                                             $containerUimap, $cache, $paramsDecorator);
            }
        }

        return $cache;
    }

    /**
     * Defines cache
     *
     * @param $elementsCollection
     * @param $elKey
     * @param $elValue
     * @param $cache
     * @param $paramsDecorator
     */
    protected function _defineCache($elementsCollection, $elKey, &$elValue, &$cache, $paramsDecorator)
    {
        if (($elementsCollection == 'tabs'
            && $elementsCollection == $elKey
            && $elValue instanceof Mage_Selenium_Uimap_TabsCollection)
            || ($elementsCollection == 'fieldsets'
                && $elementsCollection == $elKey
                && $elValue instanceof Mage_Selenium_Uimap_FieldsetsCollection)
            || $elKey == $elementsCollection
                && $elValue instanceof Mage_Selenium_Uimap_ElementsCollection
        ) {
            $cache = array_merge($cache, $elValue->getArrayCopy());
        } else {
            $this->_getElementsRecursive($elementsCollection, $elValue, $cache, $paramsDecorator);
        }
    }

    /**
     * Search UIMap element by name on any level from current and deeper
     * This method uses a cache to save search results
     *
     * @param string $elementsCollection UIMap Elements collection name
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance (by default = null)
     *
     * @return array
     */
    public function getAllElements($elementsCollection, $paramsDecorator = null)
    {
        if (empty($this->_elementsCache[$elementsCollection])) {
            $cache = array();
            $this->_elementsCache[$elementsCollection] = new Mage_Selenium_Uimap_ElementsCollection(
                $elementsCollection,
                $this->_getElementsRecursive($elementsCollection, $this->_elements, $cache, $paramsDecorator),
                $paramsDecorator);
        }

        return $this->_elementsCache[$elementsCollection];
    }

    /**
     * Magic method to call accessor methods<br>
     * Format:
     * <ul>
     * <li>- call "get"+"UIMap properties collection name"() to get UIMap elements collection by name from current level
     * <li>- call "getAll"+"UIMap properties collection name"() to get UIMap elements collection by name on any level
     * from current and deeper
     * <li>- call "find"+"UIMap element type"(element name) to get UIMap element by name on any level from current
     * and deeper
     * </ul>
     *
     * @param string $name Method's name to call 'get' | 'getAll' | 'find'
     * @param string $arguments Argument to calling method 'UIMap properties collection name' | 'UIMap element type'
     *
     * @throws Exception
     *
     * @return Mage_Selenium_Uimap_ElementsCollection|Mage_Selenium_Uimap_TabsCollection|string|null
     */
    public function __call($name, $arguments)
    {
        $returnValue = null;

        if (preg_match('|^getAll(\w+)$|', $name)) {
            $elementName = strtolower(substr($name, 6));
            $returnValue = $this->_getAll($elementName, $arguments);
        } elseif (preg_match('|^get(\w+)$|', $name)) {
            $elementName = strtolower(substr($name, 3));
            $returnValue = $this->_get($elementName, $arguments);
        } elseif (preg_match('|^find(\w+)$|', $name)) {
            $elementName = $this->_defineElementsSection($name);
            $returnValue = $this->_find($elementName, $arguments);
        }
        if (!isset($elementName)) {
            throw new Exception('Element name is undefined.');
        }

        if (!empty($elementName) && !$returnValue) {
            throw new Exception('Can\'t find element(s) "' . $elementName . '"');
        }

        return $returnValue;
    }

    /**
     * Gets all elements
     * @param $elementName
     * @param $arguments
     * @return array|null
     */
    protected function _getAll($elementName, $arguments)
    {
        $returnValue = null;
        if (!empty($elementName)) {
            $returnValue = $this->getAllElements($elementName,
                $this->_getParams(isset($arguments[1]) ? $arguments[1] : null));
        }

        return $returnValue;
    }

    /**
     * Get elements
     * @param $elementName
     * @return null
     */
    protected function _get($elementName)
    {
        $returnValue = null;
        if (!empty($elementName) && isset($this->_elements[$elementName])) {
            $returnValue = $this->_elements[$elementName];
        }

        return $returnValue;
    }

    /**
     * Defines elements name
     * @param $name
     * @return string
     */
    protected function _defineElementsSection($name)
    {
        $elementName = strtolower(substr($name, 4));
        if ($elementName === 'checkbox') {
            $elementName .= 'es';
        } else {
            $elementName .= 's';
        }
        return $elementName;
    }

    /**
     * Finds elements
     * @param $elementName
     * @param $arguments
     * @return null
     */
    protected function _find($elementName, $arguments)
    {
        $returnValue = null;
        if (!empty($elementName) && !empty($arguments)) {
            $elementsColl = $this->getAllElements($elementName);
            $returnValue = $elementsColl->get($arguments[0],
                $this->_getParams(isset($arguments[1]) ? $arguments[1] : null));
        }

        return $returnValue;
    }
}