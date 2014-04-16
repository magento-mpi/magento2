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

/**
 * Class JquerytreeElement
 * Typified element class for JqueryTree elements
 *
 * @package Mtf\Client\Element
 */
class JquerytreeElement extends Tree
{
    /**
     * Css class for finding tree nodes
     *
     * @var string
     */
    protected $nodeCssClass = 'li[data-id] > ul';
    /**
     * Css class for detecting tree nodes
     *
     * @var string
     */
    protected $nodeSelector = 'li[data-id]';
    /**
     * Css class for fetching node's name
     *
     * @var string
     */
    protected $nodeName = 'a';

    /**
     * Get structure of the tree element
     *
     * @return array
     */
    public function getStructure()
    {
        return $this->_getNodeContent($this, 'div[class*=jstree] > ul');
    }
}
