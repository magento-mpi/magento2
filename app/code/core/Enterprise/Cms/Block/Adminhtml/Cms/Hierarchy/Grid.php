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
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Pages Hierarchy Grid Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize Grid Block
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('tree_id');
        $this->setDefaultDir('asc');
    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprice_Cms_Block_Adminhtml_Cms_Hierarchy_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('enterprise_cms/hierarchy')
            ->getCollection()
            ->joinRootNode()
            ->joinPagesCount();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Enterprice_Cms_Block_Adminhtml_Cms_Hierarchy_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('tree_id', array(
            'header'    => Mage::helper('enterprise_cms')->__('ID'),
            'index'     => 'tree_id',
            'align'     => 'right',
            'width'     => 50
        ));

        $this->addColumn('label', array(
            'header'    => Mage::helper('enterprise_cms')->__('Tree Name'),
            'index'     => 'label',
            'renderer'  => 'enterprise_cms/adminhtml_cms_hierarchy_grid_renderer_label'
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('enterprise_cms')->__('URL Key'),
            'index'     => 'identifier',
            'renderer'  => 'enterprise_cms/adminhtml_cms_hierarchy_grid_renderer_identifier'
        ));

        $this->addColumn('pages_count', array(
            'header'    => Mage::helper('enterprise_cms')->__('Pages Number'),
            'index'     => 'pages_count',
            'filter'    => false
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('tree_id' => $row->getId()));
    }
}