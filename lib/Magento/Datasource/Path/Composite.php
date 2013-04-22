<?php
/**
 * Data source composite visitable element
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Datasource_Path_Composite implements Magento_Datasource_Path_Visitable
{
    protected $_children = array();

    public function __construct(Magento_ObjectManager $objectManager, $items)
    {
        foreach ($items as $key => $item) {
            $this->_children[$key] = $objectManager->create($item);
        }
    }

    public function visit(Magento_Datasource_Path_Visitor $visitor)
    {
        $result = $visitor->visitArray($this->_children);
        return $result;
    }
}