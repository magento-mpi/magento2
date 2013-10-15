<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Element as ElementInterface;
use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Class TreeElement
 * Typified element class for Tree elements
 *
 * @package Mtf\Client\Element
 */
class TreeElement extends Element
{
    /**
     * Css class for finding tree nodes
     *
     * @var string
     */
    protected $nodeCssClass = '.x-tree-node > .x-tree-node-ct';

    /**
     * Drag'n'drop method is not accessible in this class.
     * Throws exception if used.
     *
     * @param ElementInterface $target
     * @throws \BadMethodCallException
     */
    public function dragAndDrop(ElementInterface $target)
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (TreeElement)');
    }

    /**
     * setValue method is not accessible in this class.
     * Throws exception if used.
     *
     * @param string|array $value
     * @throws \BadMethodCallException
     */
    public function setValue($value)
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (TreeElement)');
    }

    /**
     * getValue method is not accessible in this class.
     * Throws exception if used.
     *
     * @throws \BadMethodCallException
     */
    public function getValue()
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (TreeElement)');

    }

    /**
     * keys method is not accessible in this class.
     * Throws exception if used.
     *
     * @param array $keys
     * @throws \BadMethodCallException
     */
    public function keys(array $keys)
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (TreeElement)');
    }

    /**
     * Click a tree element by its path in tree
     * (format numeric as 0/2/etc)
     *
     * @param string $path
     * @throws \InvalidArgumentException
     */
    public function clickByPath($path)
    {
        $structure = $this->getStructure();
        $pathArray = explode('/', $path);
        $structureChunk = $structure; //Set the root of a structure as a first structure chunk
        foreach ($pathArray as $pathChunk)
        {
            $structureChunk = $this->deep($pathChunk, $structureChunk);
        }
        if ($structureChunk) {
            $needleElement = $structureChunk->find('div > a');
            $needleElement->click();
        } else {
            throw new \InvalidArgumentException('The path specified for tree is invalid');
        }
    }

    /**
     * Internal function for deeping in hierarchy of the tree structure
     * Return the nested array if it exists or object of class Element if this is the final part of structure
     *
     * @param string $pathChunk
     * @param string $structureChunk
     * @return array|Element||false
     */
    protected function deep($pathChunk, $structureChunk)
    {
        if (isset($structureChunk[$pathChunk])){
            if (isset($structureChunk[$pathChunk]['subnodes'])) {
                return $structureChunk[$pathChunk]['subnodes'];
            } else {
                return $structureChunk[$pathChunk]['element'];
            }
        } else {
            return false;
        }
    }

    /**
     * Get structure of the tree element
     *
     * @return array
     */
    public function getStructure()
    {
        return $this->getNodeContent($this);
    }

    /**
     * Get recursive structure of the tree content
     *
     * @param Element $node
     * @return array
     */
    protected function getNodeContent($node)
    {
        $nodeArray = array();
        $nodeList = array();
        $counter = 1;
        //        $this->waitUntil(function() use ($node) {return $node->isVisible();});
        $newNode = $node->find('.x-tree-node:nth-of-type(' . $counter . ')' );

        //Get list of all children nodes to work with
        while($newNode->isVisible()) {
            $nodeList[] = $newNode;
            $counter++;
            $newNode = $node->find('.x-tree-node:nth-of-type(' . $counter . ')' );
        }

        //Write to array values of current node
        foreach ($nodeList as $currentNode) {
            /** @var Element $currentNode */
            $nodesNames = $currentNode->find('div > a > span');
            $nodesContents = $currentNode->find($this->nodeCssClass);
            $nodePresent = $nodesContents->isVisible();
            $text = $nodesNames->getText();
            $nodeArray[] = array(
                'name' => $text,
                'element' => $currentNode,
                'subnodes' => ($nodePresent)? $this->getNodeContent($nodesContents) : null
            );
        }

        return $nodeArray;
    }
}
