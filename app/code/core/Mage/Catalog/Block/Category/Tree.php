<?php




/**
 *  Catalog Category Tree block
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Moshe Gurvich <moshe@varien.com>
 */

class Mage_Catalog_Block_Category_Tree extends Mage_Core_Block_Abstract
{
	function toString()
	{
	    if ($parent = $this->getAttribute('treeParentId')) {
	        $data = Mage::getModel('catalog', 'category')->getTree($parent);
	    } else {
	        $data = Mage::getModel('catalog', 'category')->getTree();
	    }

	    $tree = new Varien_Widget_HTMLTree($data);
	    $tree->setHtmlId('category_tree');

	    $html = $tree->render();

	    return $html;
	}
}// Class Mage_Core_Block_List END