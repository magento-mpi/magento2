<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Grid extends Enterprise_Enterprise_Block_Adminhtml_Widget_Grid
{
    /**
     * Set default sort
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('giftregistryGrid');
        $this->setDefaultSort('type_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('enterprise_giftregistry/type')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('type_id', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('ID'),
            'align'  => 'right',
            'width'  => 50,
            'index'  => 'type_id'
        ));

        $this->addColumn('code', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('Code'),
            'index'  => 'code'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'    => $row->getId()
        ));
    }
}
