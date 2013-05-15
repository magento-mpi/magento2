<?php
/**
 * Data source composite visitable element
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Path_Composite implements Mage_Core_Model_Dataservice_Path_Node
{
    /**
     * @var array
     */
    protected $_children = array();

    /**
     * @param Magento_ObjectManager $objectManager
     * @param $items
     */
    public function __construct(Magento_ObjectManager $objectManager, $items)
    {
        foreach ($items as $key => $item) {
            $this->_children[$key] = $objectManager->create($item);
        }
    }

    /**
     * Returns a child path node that corresponds to the input path element.  This can be used to walk the
     * dataservice graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Mage_Core_Model_Dataservice_Path_Node|mixed|null the child node, or mixed if this is a leaf node
     */
    public function getChild($pathElement)
    {
        if (array_key_exists($pathElement, $this->_children)) {
            return $this->_children[$pathElement];
        }

        return null;
    }
}