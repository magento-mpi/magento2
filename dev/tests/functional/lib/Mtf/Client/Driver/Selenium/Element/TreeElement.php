<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

/**
 * Class TreeElement
 * Typified element class for Tree elements
 *
 * @package Mtf\Client\Element
 */
class TreeElement extends Tree
{
    /**
     * Css class for finding tree nodes
     *
     * @var string
     */
    protected $nodeCssClass = '.x-tree-node > .x-tree-node-ct';

    /**
     * Css class for detecting tree nodes
     *
     * @var string
     */
    protected $nodeSelector = '.x-tree-node';

    /**
     * Css class for fetching node's name
     *
     * @var string
     */
    protected $nodeName = 'div > a';

    /**
     * Get structure of the tree element
     *
     * @return array
     */
    public function getStructure()
    {
        return $this->_getNodeContent($this, '.x-tree-root-node');
    }
}
