<?php
/**
 * DataService composite visitable element
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService\Path;

class Composite implements \Magento\Core\Model\DataService\Path\NodeInterface
{
    /**
     * @var array
     */
    protected $_children = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param array string[] $items
     */
    public function __construct(\Magento\ObjectManager $objectManager, $items)
    {
        foreach ($items as $key => $item) {
            $this->_children[$key] = $objectManager->get($item);
        }
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * data service tree.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return \Magento\Core\Model\DataService\Path\NodeInterface|mixed|null the child node,
     *   or mixed if this is a leaf node
     */
    public function getChildNode($pathElement)
    {
        if (array_key_exists($pathElement, $this->_children)) {
            return $this->_children[$pathElement];
        }

        return null;
    }
}
