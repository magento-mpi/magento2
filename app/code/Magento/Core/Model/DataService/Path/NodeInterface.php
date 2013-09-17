<?php
/**
 * DataService path node interface.
 *
 * Think of the data service paths as forming a graph.  This interface represents a node in such a graph.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_DataService_Path_NodeInterface
{
    /**
     * Returns a child path node that corresponds to the input path element.  This can be used to walk the
     * data service graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Magento_Core_Model_DataService_Path_NodeInterface|mixed|null the child node,
     *    or mixed if this is a leaf node
     */
    public function getChildNode($pathElement);
}
