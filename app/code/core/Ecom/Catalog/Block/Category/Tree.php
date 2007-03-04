<?php

#include_once 'Ecom/Core/Block/Abstract.php';
#include_once 'Varien/Widget/HTMLTree.php';

/**
 *  Catalog Category Tree block
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Moshe Gurvich <moshe@varien.com>
 */

class Ecom_Catalog_Block_Category_Tree extends Ecom_Core_Block_Abstract
{
	function toHtml()
	{
	    if ($parent = $this->getAttribute('treeParentId')) {
	        $data = Ecom::getModel('catalog', 'category')->getTree($parent);
	    } else {
	        $data = Ecom::getModel('catalog', 'category')->getTree();
	    }

	    $tree = new Varien_Widget_HTMLTree($data);
	    $tree->setHtmlId('category_tree');

	    $html = $tree->render();

	    return $html;
	}
}// Class Ecom_Core_Block_List END