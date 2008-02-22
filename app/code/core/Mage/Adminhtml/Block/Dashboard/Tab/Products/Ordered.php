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
 * Adminhtml dashboard most ordered products grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author	   Dmytro Vasylenko <dmitriy.vasilenko@varien.com>
 */

class Mage_Adminhtml_Block_Dashboard_Tab_Products_Ordered extends Mage_Adminhtml_Block_Dashboard_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productsOrderedGrid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId((int)$this->getParam('store'))
            ->addStoreFilter($this->getParam('store'))
            ->addOrdersCount()
            ->setOrder('orders', 'desc');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'name'
        ));

        $this->addColumn('price', array(
            'header'    =>Mage::helper('reports')->__('Price'),
            'width'     =>'80px',
            'type'      =>'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'price'
        ));

        $this->addColumn('orders', array(
            'header'    =>Mage::helper('reports')->__('Orders'),
            'width'     =>'80px',
            'align'     =>'right',
            'index'     =>'orders'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }
}