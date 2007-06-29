<?php
/**
 * Adminhtml backups grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Backup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Init backups collection
     * @return void
     */
    protected function _initCollection()
    {       
        $collection = Mage::getSingleton('backup/fs_collection');
        $collection->load();
        $this->setCollection($collection);
    }

    /**
     * Configuration of grid
     */
    protected function _beforeToHtml()
    {
        $this->addColumn('time', array('header'=>__('time'), 'align'=>'center', 'index'=>'time_formated'));
        $this->addColumn('type', array('header'=>__('type'),'align'=>'center', 'index'=>'type'));
        $this->_initCollection();
        return parent::_beforeToHtml();
    }
}