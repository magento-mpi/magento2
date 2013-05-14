<?php
/**
 * Data source path visitor
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class  Mage_Core_Model_Dataservice_Path_Navigator
{
    /**
     * Searches a root node using a given path for a specific child node.
     *
     * @param Mage_Core_Model_Dataservice_Path_Node $root Root node in the graph from which to start the search.
     * @param array $path path to use for searching.
     * @return Mage_Core_Model_Dataservice_Path_Node
     */
    public function search(Mage_Core_Model_Dataservice_Path_Node $root, array $path)
    {
        $pathElement = array_shift($path);
        $childElement = $root->getChild($pathElement);

        if (empty($path)) {
            return $childElement;
        }

        return $this->search($childElement, $path);
    }
}