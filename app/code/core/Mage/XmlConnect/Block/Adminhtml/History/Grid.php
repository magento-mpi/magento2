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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect application history grid
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('app_history_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');

    }

    /**
     * Setting collection to show
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xmlconnect/history')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header'    => Mage::helper('xmlconnect')->__('Application Title'),
            'align'     => 'left',
            'index'     => 'title',
            'type'      => 'text',
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('xmlconnect')->__('Application Code'),
            'align'     => 'left',
            'index'     => 'code',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('xmlconnect')->__('Date Submitted'),
            'align'     => 'left',
            'index'     => 'created_at',
            'type'      => 'datetime'
        ));

        $this->addColumn('activation_key', array(
            'header'    => Mage::helper('xmlconnect')->__('Activation Key'),
            'align'     => 'left',
            'index'     => 'activation_key',
        ));
        return parent::_prepareColumns();
    }

    /**
     * Remove row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
