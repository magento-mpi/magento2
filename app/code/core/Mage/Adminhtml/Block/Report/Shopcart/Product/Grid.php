<?php
/**
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml products in carts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Shopcart_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {          
        $collection = Mage::getResourceModel('reports/product_collection')
          ->addAttributeToSelect('price');
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()
            ->addCartsCount()
            ->addOrdersCount();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ));
        
        $this->addColumn('name', array(
            'header'    =>__('Product Name'),
            'index'     =>'name'
        ));    
        
        $this->addColumn('price', array(
            'header'    =>__('Price'),
            'width'     =>'70px',
            'type'      =>'currency',
            'align'     =>'right',
            'currency_code' => (string) Mage::getStoreConfig('general/currency/base'),
            'index'     =>'price'
        ));
 
        $this->addColumn('carts', array(
            'header'    =>__('Carts'),
            'width'     =>'70px',
            'sortable'  =>false,
            'align'     =>'right',
            'index'     =>'carts'
        ));
        
        $this->setFilterVisibility(false);
        return parent::_prepareColumns();
    }    
}
