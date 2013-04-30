<?php
/**
 * Data source composite visitable element
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Path_Composite implements Mage_Core_Model_Dataservice_Path_Visitable
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
     * @param Mage_Core_Model_Dataservice_Path_Visitor $visitor
     * @return null
     */
    public function visit(Mage_Core_Model_Dataservice_Path_Visitor $visitor)
    {
        $result = $visitor->visitArray($this->_children);
        return $result;
    }
}