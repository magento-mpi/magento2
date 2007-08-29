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
 * Adminhtml backups grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Backup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('backupsGrid');
		$this->setDefaultSort('time', 'desc');       
    }

    /**
     * Init backups collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('backup/fs_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $gridUrl = Mage::getUrl('*/*/');
        
        $this->addColumn('time', array(
                                'header'=>__('Time'),
                                'index'=>'time_formated',
                                'type' => 'datetime')
                                );
        $this->addColumn('type', array(
                                'header'=>__('Type'),
                                'filter'    => 'adminhtml/backup_grid_filter_type',
                                'renderer'    => 'adminhtml/backup_grid_renderer_type',
                                'index'=>'type')
                                );
        $this->addColumn('download', array('header'=>__('Download'),
                                           'format'=>'<a href="' . $gridUrl .'download/time/$time/type/$type/file/sql/">sql</a><span class="separator">&nbsp;|&nbsp;</span><a href="' . $gridUrl .'download/time/$time/type/$type/file/gz/">gz</a>',
                                           'index'=>'type', 'sortable'=>false, 'filter' => false));
        $this->addColumn('action', array(
                                'header'=>__('Action'),
                                'type' => 'action',
                                'width' => '80px',
                                'filter' => false,
                                'sortable' => false,
                                'actions' => array(
                                    array(
                                        'url' => $gridUrl .'delete/time/$time/type/$type/',
                                        'caption' => __('Delete'),
                                        'confirm' => __('Are you sure you want to do this?')
                                    )
                                ),
                                'index'=>'type', 'sortable'=>false));
        return $this;
    }

}
