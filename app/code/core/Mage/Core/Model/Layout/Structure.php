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
        $element = $this->_getElementByXpath("//element[@name='$name']");
        if ($element) {
            $this->_fixBrokenRefByName($name);
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
        $this->_fixBrokenRef($parentName);
        $children = array();
        /** @var $child DOMElement */
        foreach ($this->_findByXpath("//element[@name='$parentName']/element") as $child) {
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
        $element = $this->_getElementByXpath("//element[@name='$name']");
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
        $element = $this->_getElementByXpath("//element[@name='$name']");
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
        $parent = $this->_getElementByXpath("//element[@name='$parentName']");
        if ($parent) {
            $child = $this->_getElementByXpath("element[@alias='$alias']", $parent);
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
        $element = $this->_getElementByXpath("element[@name='$name']");
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
        $this->_fixBrokenRef($parentName, $alias);
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
        $options = array()
    ) {
        if (!in_array($type, array(self::ELEMENT_TYPE_BLOCK, self::ELEMENT_TYPE_CONTAINER))) {
            return false;
        }

        if (empty($name)) {
            $name = 'STRUCTURE_TMP_NAME_' . ($this->_nameIncrement++);
        }
        if ($alias == '') {
            $alias = $name;
        }

        $child = $this->_dom->createElement('element');
        $child->setAttribute('type', $type);
        $child->setAttribute('name', $name);
        $child->setAttribute('alias', $alias);
        foreach ($options as $optName => $value) {
            $child->setAttribute($optName, $value);
        }

        $parentNode = false;
        if ($parentName) {
            $parentNode = $this->_getElementByName($parentName);
            if (!$parentNode) {
                $child->setAttribute('broken_parent_name', $parentName);
                $sibling = '';
                $after = true;
            }
        }
        if (!$parentNode) {
            $parentNode = $this->_dom->firstChild;
        }

        $this->_clearExistingChild($parentNode, $alias);

        $siblingNode = $this->_getSiblingElement($parentNode, $after, $sibling);
        if ($siblingNode) {
            $parentNode->insertBefore($child, $siblingNode);
        } else {
            $parentNode->appendChild($child);
        }

        return $child->getAttribute('name');
    }

    /**
     * Get sibling element based on after and siblingName parameter
     *
     * @param DOMElement $parentNode
     * @param string $after
     * @param string $siblingName
     * @return DOMElement|bool
     */
    protected function _getSiblingElement(DOMElement $parentNode, $after, $siblingName)
    {
        if (!$parentNode->hasChildNodes()) {
            $siblingName = '';
        }
        $siblingNode = false;
        if ('' !== $siblingName) {
            $siblingNode = $this->_getChildElement($parentNode->getAttribute('name'), $siblingName);
            if ($siblingNode && $after) {
                if (isset($siblingNode->nextSibling)) {
                    $siblingNode = $siblingNode->nextSibling;
                } else {
                    $siblingNode = false;
                }
            }
        }
        if (!$after && !$siblingNode && isset($parentNode->firstChild)) {
            $siblingNode = $parentNode->firstChild;
        }

        return $siblingNode;
    }

    /**
     * Remove existing child element
     *
     * @param DOMElement $parentNode
     * @param string $alias
     * @return bool
     */
    protected function _clearExistingChild(DOMElement $parentNode, $alias)
    {
        $exist = $this->_getChildElement($parentNode->getAttribute('name'), $alias);
        if ($exist) {
            $parentNode->removeChild($exist);
            return true;
        }
        return false;
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
        return $this->_findByXpath("//element[@name='$name']")->length > 0;
    }

    /**
     * Get children count
     *
     * @param string $parentName
     * @return int
     */
    public function getChildrenCount($parentName)
    {
        $this->_fixBrokenRef($parentName);
        return $this->_findByXpath("//element[@name='$parentName']/element")->length;
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
        if ($this->_getElementByXpath("groups/group[@name='$parentGroupName']/child[@name='$name']", $parentElement)) {
            return false;
        }

        $group = $this->_getElementByXpath("groups/group[@name='$parentGroupName']", $parentElement);
        if (!$group) {
            $groups = $this->_getElementByXpath('groups', $parentElement);
            if (!$groups) {
                $groups = $this->_dom->createElement('groups');
                $parentElement->appendChild($groups);
            }
            $group = $this->_dom->createElement('group');
            $groups->appendChild($group);
            $group->setAttribute('name', $parentGroupName);
        }

        $child = $this->_dom->createElement('child');
        $group->appendChild($child);
        $child->setAttribute('name', $name);

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
        $elements = $this->_findByXpath("//element[@name='$name']/groups/group[@name='$groupName']/child");
        /** @var $element DOMElement */
        foreach ($elements as $element) {
            $children[] = $element->getAttribute('name');
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
        return $this->_findByXpath("//element[@name='$name' and @type='block']")->length > 0;
    }

    /**
     * Get child node from a parent
     *
     * @param string $parentName
     * @param string $alias
     * @return DOMElement|bool
     */
    protected function _getChildElement($parentName, $alias)
    {
        if (!$alias) {
            return false;
        }
        $this->_fixBrokenRef($parentName, $alias);
        $parentName = $this->_getElementByXpath("//element[@name='$parentName']");
        return $this->_getElementByXpath("element[@alias='$alias']", $parentName);
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
            $parentNode = $this->_getElementByXpath("//element[@name='$newParent']");
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
        return $this->_getElementByXpath("//element[@name='$name']");
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

    /**
     * A shortcut to _fixBrokenRef in case if parent name is unknown
     *
     * @param string $elementName
     */
    protected function _fixBrokenRefByName($elementName)
    {
        $element = $this->_getElementByXpath("//element[@name='{$elementName}' and @broken_parent_name]");
        if (!$element) {
            return;
        }
        $this->_fixBrokenRef($element->getAttribute('broken_parent_name'), $element->getAttribute('alias'));
    }

    /**
     * Move an element by specified parent and alias, if it is not found on proper place (crutch-fix)
     *
     * @param string $parentName
     * @param string $alias
     */
    protected function _fixBrokenRef($parentName, $alias = null)
    {
        $xpath = (null === $alias) ? "//element[@broken_parent_name='$parentName']"
            : "//element[@broken_parent_name='$parentName' and @alias='$alias']";
        foreach ($this->_findByXpath($xpath) as $element) {
            $this->setChild($parentName, $element->getAttribute('name'), $element->getAttribute('alias'));
            $element->removeAttribute('broken_parent_name');
        }
    }
}
