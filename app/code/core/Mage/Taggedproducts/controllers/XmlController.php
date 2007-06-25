<?php
/**
 * Page Index Controller
 *
 * @copyright  Varien, 2007
 */

class Mage_Taggedproducts_XmlController extends Mage_Core_Controller_Front_Action {
    function indexAction() {    	
        $this->xmlAction();
    }
    
    function xmlAction() {
    	$tag = $this->getRequest()->getParam('tag');
    	$collection = Mage::getResourceModel('catalog/product_collection');
        $collection = $collection->addTagFilter($tag);
        
        $doc = new DOMDocument("1.0", "utf-8");
        $root = $doc->createElement("rss");
        
        $root->setAttribute("version", "2.0");
        $doc->appendChild($root);
        
        $channel = $doc->createElement("channel");
        $root->appendChild($channel);
        
        $channel->appendChild($doc->createElement("title", "Products tagged with TAG \"{$tag}\""));
        $channel->appendChild($doc->createElement("generator", "Generic generator by Varien"));
        
        foreach ($collection as $product) {
        	$item = $doc->createElement("item");
        	$channel->appendChild($item);
        	
        	$item->appendChild($doc->createElement("title", htmlspecialchars($product['name'])));
        	$item->appendChild($doc->createElement("description", htmlspecialchars($product['description'])));
        	$item->appendChild($doc->createElement("link", "http://magento-alexey.kiev-dev/catalog/product/view/id/{$product['product_id']}/"));
        }
        header("Content-type: application/xhtml+xml");
        echo $doc->saveXML();
    }    
}