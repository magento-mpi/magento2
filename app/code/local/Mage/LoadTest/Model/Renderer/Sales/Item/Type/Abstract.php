<?php
/* 
 * Magento
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 * 
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


abstract class Mage_LoadTest_Model_Renderer_Sales_Item_Type_Abstract {

    protected $_products = array();
    protected $_product = null;
    protected $_typeInstance = null;

    public function __construct($params = array())
    {
	/*$product_id = $params['product']->getId();
	if(!isset($this->_products[$product_id]))
	    $this->_products[$product_id] = $params['product'];
	$this->_product = $this->_products[$product_id];
	$this->_typeInstance = $this->_product->getTypeInstance();*/
    }

    protected function _getAllowedQty()
    {
	$qty = 1;
	if ($max = $this->_product->getStockItem()->getQty()) {
	    $qty = rand(1, $max);
        }
	return $qty;
    }

    abstract public function prepareRequestForCart($_product);
}


