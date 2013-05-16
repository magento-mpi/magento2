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
     * @param Mage_Core_Model_Dataservice_Path_Node|array $root Root node in the graph from which to start the search.
     * @param array $path path to use for searching.
     * @return mixed
     */
    public function search($root, array $path)
    {
        $pathElement = array_shift($path);

        $childElement = null;
        if (is_array($root)) {
            if (array_key_exists($pathElement, $root)) {
                $childElement = $root[$pathElement];
            }
        } else {
            $childElement = $root->getChild($pathElement);
        }

        if (empty($path)) {
            return $childElement;
        }

        return $this->search($childElement, $path);
    }
}