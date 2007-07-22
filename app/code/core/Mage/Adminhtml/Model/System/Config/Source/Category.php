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
        $tree->getCategoryCollection()
                ->addAttributeToSelect('name');
        $nodes = $tree->load(1, 1)
            ->getRoot()
            ->getChildren();
        $options = array();
        
        $options[] = array(
            'label' => __('Chose category...'),
            'value' => ''
        );
        foreach ($nodes as $node) {
        	$options[] = array(
        	   'label' => $node->getName(),
        	   'value' => $node->getId()
        	);
        }
        return $options;
    }
}
