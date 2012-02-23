<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout structure model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Layout_Structure
{
    /** Available element types */
    const ELEMENT_TYPE_BLOCK = 'block';
    const ELEMENT_TYPE_CONTAINER = 'container';

    /**
     * Page structure as DOM document
     *
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * Xpath object
     *
     * @var DOMXPath
     */
    protected $_xpath;

    /**
     * Increment for temporary names of elements
     *
     * @var int
     */
    protected $_nameIncrement = 0;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        $this->_dom = new DOMDocument();
        $this->_dom->formatOutput = true;
        $this->_dom->loadXML("<layout/>");
        $this->_xpath = new DOMXPath($this->_dom);
    }

    /**
     * Get parent element by name
     *
     * @param $name
     * @return bool|string
     */
    public function getParentName($name)
    {
        $element = $this->_getElementByXpath("//*[@name='$name']");
        if ($element) {
            return $element->parentNode->getAttribute('name');
        }
        return false;
    }

    /**
     * Get sorted list of child aliases by parent name
     *
     * @param $parentName
     * @return array
     */
    public function getChildNames($parentName)
    {
        $children = array();
        /** @var $child DOMElement */
        foreach ($this->_findByXpath("//*[@name='$parentName']/*") as $child) {
            $children[] = $child->getAttribute('name');
        }
        return $children;
    }

    /**
     * Move node to necessary parent node. If node doesn't exist, creates it
     *
     * @param $parentName
     * @param $elementName
     * @param $alias
     * @return Mage_Core_Model_Layout_Structure
     */
    public function setChild($parentName, $elementName, $alias)
    {
        $element = $this->_getElementByName($elementName);
        if (!$element) {
            $this->insertBlock($parentName, $elementName, $alias);
            return $this;
        } else {
            $element->setAttribute('alias', $alias);
        }

        $this->_move($element, $parentName);

        return $this;
    }

    /**
     * Get element alias by name
     *
     * @param $name
     * @return string
     */
    public function getElementAlias($name)
    {
        return $this->getElementAttribute($name, 'alias');
    }

    /**
     * Set element attribute
     *
     * @param string $name
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function setElementAttribute($name, $attribute, $value)
    {
        /** @var $element DOMElement */
        $element = $this->_getElementByXpath("//*[@name='$name']");
        if (!$element) {
            return false;
        }
        $element->setAttribute($attribute, $value);

        return true;
    }

    /**
     * Set element attribute
     *
     * @param string $name
     * @param string $attribute
     * @return string
     */
    public function getElementAttribute($name, $attribute)
    {
        /** @var $element DOMElement */
        $element = $this->_getElementByXpath("//*[@name='$name']");
        if ($element && $element->hasAttribute($attribute)) {
            return $element->getAttribute($attribute);
        }

        return '';
    }

    /**
     * Move child element to new parent
     *
     * @param $childName
     * @param $parent
     * @return Mage_Core_Model_Layout_Structure
     */
    public function move($childName, $parent)
    {
        $child = $this->_getElementByName($childName);
        if ($child) {
            $this->_move($child, $parent);
        }

        return $this;
    }

    /**
     * Remove child from parent element
     *
     * @param $parentName
     * @param $alias
     * @return Mage_Core_Model_Layout_Structure
     */
    public function unsetChild($parentName, $alias)
    {
        $parent = $this->_getElementByXpath("//*[@name='$parentName']");
        if ($parent) {
            $child = $this->_getElementByXpath("*[@alias='$alias']", $parent);
            if ($child) {
                $parent->removeChild($child);
            }
        }

        return $this;
    }

    /**
     * Remove element from the structure
     *
     * @param string $name
     * @return bool
     */
    public function unsetElement($name)
    {
        $element = $this->_getElementByXpath("*[@name='$name']");
        if ($element) {
            $this->_dom->removeChild($element);
        }

        return true;
    }

    /**
     * Get child name by parent name and alias
     *
     * @param $parentName
     * @param $alias
     * @return string|bool
     */
    public function getChildName($parentName, $alias)
    {
        $child = $this->_getChildElement($parentName, $alias);
        if (!$child) {
            return false;
        }
        return $child->getAttribute('name');
    }

    /**
     * Add new element to necessary position in the structure
     *
     * @param string $parentName
     * @param string $name
     * @param string $type
     * @param string $alias
     * @param string $sibling
     * @param bool $after
     * @param array $options
     * @return string|bool
     */
    public function insertElement($parentName, $name, $type, $alias = '', $after = true, $sibling = '',
                                      $options = array())
    {
        if (!in_array($type, array(self::ELEMENT_TYPE_BLOCK, self::ELEMENT_TYPE_CONTAINER))) {
            return false;
        }

        if (empty($name)) {
            $name = 'STRUCTURE_TMP_NAME_' . ($this->_nameIncrement++);
        }

        $parentNode = false;
        if ($parentName) {
            $parentNode = $this->_getElementByXpath("//*[@name='$parentName']");
        }
        if (!$parentNode) {
            $parentNode = $this->_dom->firstChild;
        }

        if ($alias == '') {
            $alias = $name;
        }
        $exist = $this->_getElementByXpath("*[@alias='$alias']", $parentNode);
        if ($exist) {
            $parentNode->removeChild($exist);
        }

        $child = new DOMElement($type);
        if ('' !== $sibling && $parentNode->hasChildNodes()) {
            $siblingNode = $this->_getElementByXpath("*[@alias='$sibling']", $parentNode);
            if (!$siblingNode) {
                $siblingNode = $parentNode->lastChild;
            }
            if ($after) {
                $parentNode->insertBefore($child, $siblingNode->nextSibling);
            } else {
                $parentNode->insertBefore($child, $siblingNode);
            }
        } else {
            if ($after || !$parentNode->hasChildNodes()) {
                $parentNode->appendChild($child);
            } else {
                $parentNode->insertBefore($child, $parentNode->firstChild);
            }
        }

        $child->setAttribute('name', $name);
        $child->setAttribute('alias', $alias);

        foreach ($options as $optName => $value) {
            $child->setAttribute($optName, $alias);
        }

        return $child->getAttribute('name');
    }

    /**
     * Add new block to necessary position in the structure
     *
     * @param $parentName
     * @param $name
     * @param string $alias
     * @param string $sibling
     * @param bool $after
     * @param array $options
     * @return string|bool
     */
    public function insertBlock($parentName, $name, $alias = '', $after = true, $sibling = '', $options = array())
    {
        return $this->insertElement($parentName, $name, 'block', $alias, $after, $sibling, $options);
    }

    /**
     * Add new container to necessary position in the structure
     *
     * @param $parentName
     * @param $name
     * @param string $alias
     * @param string $sibling
     * @param bool $after
     * @param array $options
     * @return string|bool
     */
    public function insertContainer($parentName, $name, $alias = '', $after = true, $sibling = '', $options = array())
    {
        return $this->insertElement($parentName, $name, 'container', $alias, $after, $sibling, $options);
    }

    /**
     * Check if element with specified name exists in the structure
     *
     * @param $name
     * @return bool
     */
    public function hasElement($name)
    {
        return $this->_findByXpath("//*[@name='$name']")->length > 0;
    }

    /**
     * Get children count
     *
     * @param string $parentName
     * @return int
     */
    public function getChildrenCount($parentName)
    {
        return $this->_findByXpath("//*[@name='$parentName']/*")->length;
    }

    /**
     * Add element to parent group
     *
     * @param string $name
     * @param string $parentName
     * @param string $parentGroupName
     * @return bool
     */
    public function addToParentGroup($name, $parentName, $parentGroupName)
    {
        $parentElement = $this->_getElementByName($parentName);
        if ($this->_getElementByXpath("groups/group[@name='$parentGroupName']/node[title='$name']", $parentElement)) {
            return false;
        }

        $group = $this->_getElementByXpath("groups/group[@groupName='$parentGroupName']", $parentElement);
        if (!$group) {
            $groups = $this->_getElementByXpath('groups', $parentElement);
            if (!$groups) {
                $groups = new DOMElement('groups');
                $parentElement->appendChild($groups);
            }
            $group = new DOMElement('group');
            $groups->appendChild($group);
            $group->setAttribute('groupName', $parentGroupName);
        }

        $child = new DOMNode();
        $group->appendChild($child);
        $child->textContent = $name;

        return true;
    }

    /**
     * Get element names for specified group
     *
     * @param string $name
     * @param string $groupName
     * @return array
     */
    public function getGroupChildNames($name, $groupName)
    {
        $children = array();
        $elements = $this->_findByXpath("//*[@name='$name']/groups/group[@groupName='$groupName']*");
        /** @var $element DOMNode */
        foreach ($elements as $element) {
            $children[] = $element->textContent;
        }

        return $children;
    }

    /**
     * Check if element is block
     *
     * @param string $name
     * @return bool
     */
    public function isBlock($name)
    {
        $element = $this->_getElementByXpath("//*[@name='$name']");
        return $element && (self::ELEMENT_TYPE_BLOCK == $element->nodeName);
    }

    public function getStartNode()
    {
        file_put_contents('e:/start.xml', $this->_dom->saveXML());
    }

    /**
     * Get child node from a parent
     *
     * @param $parent
     * @param string $alias
     * @return DOMElement|bool
     */
    protected function _getChildElement($parent, $alias)
    {
        if (!$parent || !$alias) {
            return false;
        }
        $parent = $this->_getElementByXpath("//*[@name='$parent']");
        return $this->_getElementByXpath("*[@alias='$alias']", $parent);
    }

    /**
     * Move element to new parent node
     *
     * @param DOMElement $element
     * @param string $newParent
     */
    protected function _move($element, $newParent)
    {
        $parentNode = false;
        if ($newParent) {
            $parentNode = $this->_getElementByXpath("//*[@name='$newParent']");
        }
        if (!$parentNode) {
            $parentNode = $this->_dom->firstChild;
        }

        $parentNode->appendChild($element);
    }

    /**
     * Get element by name
     *
     * @param string $name
     * @return DOMElement
     */
    protected function _getElementByName($name)
    {
        return $this->_getElementByXpath("//*[@name='$name']");
    }

    /**
     * Find element(s) by xpath
     *
     * @param string $xpath
     * @param DOMElement $context
     * @return DOMNodeList
     */
    protected function _findByXpath($xpath, $context = null)
    {
        return $this->_xpath->query($xpath, $context);
    }

    /**
     * Get first element by xpath
     *
     * Gets element by xpath
     *
     * @param $xpath
     * @param null|DOMElement $context
     * @return null|DOMElement
     */
    protected function _getElementByXpath($xpath, $context = null)
    {
        $elements = $this->_xpath->query($xpath, $context);
        if ($elements) {
            return $elements->item(0);
        } else {
            return null;
        }
    }
}
