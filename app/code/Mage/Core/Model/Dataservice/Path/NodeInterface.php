<?php
/**
 * Data service path node.
 *
 * Think of the data service paths as forming a graph.  This interface represents a node in such a graph.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Dataservice_Path_NodeInterface
{
    /**
     * Returns a child path node that corresponds to the input path element.  This can be used to walk the
     * dataservice graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Mage_Core_Model_Dataservice_Path_NodeInterface|mixed|null the child node, or mixed if this is a leaf node
     */
    public function getChildNode($pathElement);
}