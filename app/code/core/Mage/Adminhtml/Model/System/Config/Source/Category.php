<?php
/**
 * Config category source
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Category
{
    public function toOptionArray()
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        
        $collection = Mage::getResourceModel('catalog/category_collection');
        $collection->getEntity()
            ->setStore(0);
        $collection->addAttributeToSelect('name')
            ->addFieldToFilter('parent_id', 1)
            ->load();

        $options = array();
        
        $options[] = array(
            'label' => '',
            'value' => ''
        );
        foreach ($collection as $category) {
            $options[] = array(
               'label' => $category->getName(),
               'value' => $category->getId()
            );
        }
        
        return $options;
    }
}
