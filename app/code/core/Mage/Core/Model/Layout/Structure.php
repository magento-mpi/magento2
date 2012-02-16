<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_Structure
{
    const ELEMENT_TYPE_BLOCK = 'block';
    const ELEMENT_TYPE_CONTAINER = 'container';

    /**
     * Layout model
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Page structure as XML document
     *
     * @var DOMDocument
     */
    protected $_document;

    /**
     * Xpath object
     *
     * @var DOMXPath
     */
    protected $_xpath;

    protected $_availableOptions = array(
        self::ELEMENT_TYPE_BLOCK => array(),
        self::ELEMENT_TYPE_CONTAINER => array(
            'label', 'htmlId', 'htmlClass', 'htmlTagName',
        ),
    );

    public function __construct(Mage_Core_Model_Layout $layout)
    {
        $this->_layout = $layout;
        $this->_document = new DOMDocument();
        $this->_document->formatOutput = true;
        $this->_document->loadXML("<layout/>");
        $this->_xpath = new DOMXPath($this->_document);
    }

    /**
     * @param $name
     * @return bool|DOMNode
     */
    public function getParentElement($name)
    {
        $element = $this->_findByXpath("//*[@name='$name']")->item(0);
        if ($element) {
            return $this->getElementObject($element->parentNode);
        }
        return false;
    }

    /**
     * @param $parentName
     * @return DOMNodeList
     */
    public function getSortedChildren($parentName)
    {
        $children = array();
        /** @var $child DOMElement */
        foreach ($this->getSortedChildrenList($parentName) as $child) {
            $children[] = $child->getAttribute('name');
        }
        return $children;
    }

    /**
     * @param $parentName
     * @return DOMNodeList
     */
    public function getSortedChildrenList($parentName)
    {
        return $this->_findByXpath("//*[@name='$parentName']/*");
    }

    /**
     * @param $parentName
     * @param $elementName
     * @param $alias
     * @return Mage_Core_Model_Layout_Structure
     */
    public function setChild($parentName, $elementName, $alias)
    {
        $element = $this->getElementByName($elementName);
        if (!$element) {
            $block = $this->getLayout()->getBlock($elementName);
            if ($block) {
                $block->setAlias($alias);
                $this->insertElement($parentName, $elementName, 'block', $alias);
            }
            return $this;
        } else {
            $element->setAttribute('alias', $alias);
        }
        $elementType = $element->nodeName;

        if (self::ELEMENT_TYPE_BLOCK == $elementType) {
            $block = $this->getLayout()->getBlock($element->getAttribute('name'));
            if ($block->isAnonymous()) {
                $suffix = $block->getAnonSuffix();
                if (empty($suffix)) {
                    $suffix = 'child' . $this->getChildrenCount($parentName);
                }
                $blockName = $parentName . '.' . $suffix;

                if ($this->getLayout()) {
                    $this->getLayout()->unsetBlock($block->getNameInLayout())
                        ->setBlock($blockName, $block);
                    $this->_updateElementName($block->getNameInLayout(), $blockName);
                }

                $block->setNameInLayout($blockName);
                $block->setIsAnonymous(false);

            }
            if (empty($alias)) {
                $alias = $block->getNameInLayout();
            }
            $block->setBlockAlias($alias);
        }
        $this->_move($element, $parentName);

        return $this;
    }

    /**
     * @param $oldName
     * @param $newName
     * @return Mage_Core_Model_Layout_Structure
     */
    protected function _updateElementName($oldName, $newName)
    {
        /** @var $element DOMElement */
        $element = $this->_findByXpath("//*[@name='$oldName']")->item(0);
        if ($element) {
            $element->setAttribute('name', $newName);
        }

        return $this;
    }

    /**
     * @param DOMElement $element
     * @param string $newParent
     */
    protected function _move($element, $newParent)
    {
        if ($newParent) {
            $parentNodes = $this->_findByXpath("//*[@name='$newParent']");
            if ($parentNodes->length > 1) {
                throw new Magento_Exception("Found more than one parent '$newParent");
            } elseif ($parentNodes->length == 1) {
                $parentNode = $parentNodes->item(0);
            }
        }
        if (!isset($parentNode)) {
            $parentNode = $this->_document->firstChild;
        }

        $parentNode->appendChild($element);
    }

    /**
     * @param $parentName
     * @param $alias
     * @return Mage_Core_Model_Layout_Structure
     */
    public function unsetChild($parentName, $alias)
    {
        $parent = $this->_findByXpath("//*[@name='$parentName'")->item(0);
        if ($parent) {
            $child = $this->_findByXpath("*[@alias='$alias']", $parent)->item(0);
            if ($child) {
                $parent->removeChild($child);
            }
        }

        return $this;
    }

    /**
     * @param $parentName
     * @param $alias
     * @param $callback
     * @param $result
     * @param $params
     * @return Mage_Core_Model_Layout_Structure
     */
    public function unsetCallChild($parentName, $alias, $callback, $result, $params)
    {
        $child = $this->getChild($parentName, $alias);
        if ($this->isBlock($child)) {
            if ($child) {
                $args     = func_get_args();
                $alias    = array_shift($args);
                $result   = (string)array_shift($args);
                $callback = array_shift($args);
                if (!is_array($params)) {
                    $params = $args;
                }

                if ($result == call_user_func_array(array(&$child, $callback), $params)) {
                    $this->unsetChild($parentName, $alias);
                }
            }
        }

        return $this;
    }

    /**
     * @param $parent
     * @return Mage_Core_Model_Layout_Structure
     */
    public function unsetChildren($parent)
    {
        $parent = $this->_findByXpath("//*[@name='$parent']")->item(0);
        if ($parent) {
            foreach ($parent->childNodes as $child) {
                $parent->removeChild($child);
            }
        }

        return $this;
    }

    /**
     * @param $parent
     * @param string $alias
     * @return array|Mage_Core_Block_Abstract|null
     */
    public function getChild($parent, $alias = '')
    {
        $result = null;
        $element = $this->getChildElement($parent, $alias);
        if ($element && $element instanceof DOMNode) {
            $result = $this->getLayout()->getBlock($element->getAttribute('name'));
        } else {
            foreach ($element as $child) {
                $result[] = $this->getElementObject($child);
            }
        }

        return $result;
    }

    /**
     * @param $parent
     * @param string $alias
     * @return DOMNodeList|DOMElement
     */
    public function getChildElement($parent, $alias = '')
    {
        $parent = $this->_findByXpath("//*[@name='$parent']")->item(0);
        if ('' === $alias) {
            return $parent->childNodes;
        } else {
            return $this->_findByXpath("*[@alias='$alias']", $parent)->item(0);
        }
    }

    /**
     * @param string $parentName
     * @return array
     */
    public function getSortedChildrenElements($parentName)
    {
        $children = array();
        foreach ($this->getSortedChildren($parentName) as $child) {
            $children[$child->attribute['name']] = $this->getElementObject($child);
        }
        return $children;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getElementHtml($name)
    {
        $html = '';
        $element = $this->getElementByName($name);
        if ($this->isBlock($element)) {
            $block = $this->getLayout()->getBlock($element->getAttribute('name'));
            $html = $block->toHtml();
        } else {
            /** @var $child DOMElement */
            foreach ($element->childNodes as $child) {
                $html .= $this->getElementHtml($child->getAttribute('name'));
            }
        }
        return $html;
    }

    /**
     * @param $parentName
     * @param $name
     * @param $type
     * @param $alias
     * @param string $sibling
     * @param bool $after
     * @return bool|DOMElement
     * @throws Magento_Exception
     */
    public function insertElement($parentName, $name, $type, $alias, $sibling = '', $after = true)
    {
        if (!in_array($type, array(self::ELEMENT_TYPE_BLOCK, self::ELEMENT_TYPE_CONTAINER))) {
            return false;
        }

        if ($parentName) {
            $parentNodes = $this->_findByXpath("//*[@name='$parentName']");
            if ($parentNodes->length == 1) {
                $parentNode = $parentNodes->item(0);
            }
        }
        if (!isset($parentNode)) {
            $parentNode = $this->_document->firstChild;
        }

        if ($exist = $this->_findByXpath("*[@alias='$alias']", $parentNode)->item(0)) {
            $parentNode->removeChild($exist);
        }

        $child = new DOMElement($type);
        if ('' !== $sibling) {
            $siblingNode = $this->_findByXpath("*[@alias='$sibling']", $parentNode)->item(0);
            if (is_null($siblingNode)) {
                $siblingNode = $parentNode->lastChild;
            }
            if ($after) {
                $parentNode->insertBefore($child, $siblingNode->nextSibling);
            } else {
                $parentNode->insertBefore($child, $siblingNode);
            }
        } else {
            if ($after) {
                $parentNode->appendChild($child);
            } else {
                $parentNode->insertBefore($child, $parentNode->firstChild);
            }
        }

        $child->setAttribute('name', $name);
        $child->setAttribute('alias', $alias);

        return $child;
    }

    /**
     * @param DOMElement $element
     * @param Mage_Core_Model_Layout_Element $node
     * @return DOMElement
     */
    public function extendAttributes($element, $node)
    {
        if (isset($this->_availableOptions[$element->nodeName])) {
            foreach ($this->_availableOptions[$element->nodeName] as $name) {
                $parameter = $node->getAttribute($name);
                if (!is_null($parameter)) {
                    $element->setAttribute($name, $parameter);
                }
            }
        }

        return $element;
    }

    /**
     * @return string
     */
    public function getDocument()
    {
        return $this->_document->saveXML();
    }

    /**
     * @param string $parentName
     * @return int
     */
    public function getChildrenCount($parentName)
    {
        return $this->_findByXpath("//*[@name='$parentName']/*")->length;
    }

    /**
     * @param string $parent
     * @param string $alias
     * @return bool|Mage_Core_Block_Abstract
     */
    public function getElementByAlias($parent, $alias)
    {
        return $this->_findByXpath("//*[@name='$parent']/*[@alias='$alias'")->item(0);
    }

    /**
     * @param string $name
     * @return DOMElement
     */
    public function getElementByName($name)
    {
        $elements = $this->_findByXpath("//*[@name='$name']");
        if ($elements) {
            return $elements->item(0);
        } else {
            return null;
        }
    }

    /**
     * @param DOMElement $element
     * @return Mage_Core_Block_Abstract
     */
    public function getElementObject($element)
    {
        if ($this->isBlock($element)) {
            $element = $this->getLayout()->getBlock($element->getAttribute('name'));
        }
        return $element;
    }

    /**
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @param $element
     * @return bool
     */
    public function isBlock($element)
    {
        return ($element instanceof Mage_Core_Block_Abstract) ||
            ($element instanceof DOMElement && self::ELEMENT_TYPE_BLOCK == $element->nodeName);
    }

    /**
     * @param string $xpath
     * @param DOMElement $context
     * @return DOMNodeList
     */
    protected function _findByXpath($xpath, $context = null)
    {
        return $this->_xpath->query($xpath, $context);
    }
}
