<?php
/**
 * Adminhtml customers groups grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    private $_gzInstalled = false;
    
    public function __construct()
    {
        $this->_gzInstalled = extension_loaded('zlib');
        parent::__construct();
    }
    
    /**
     * Init customer groups collection
     * @return void
     */
    protected function _initCollection()
    {       
        $collection = Mage::getResourceSingleton('customer/group_collection');
        $collection->load();
        print_r($collection->getSize());
        $this->setCollection($collection);
    }

    /**
     * Configuration of grid
     */
    protected function _beforeToHtml()
    {
        $gridUrl = Mage::getUrl('adminhtml',array('controller'=>'customer_group'));
                
        $this->addColumn('time', array('header'=>__('id'), 'sortable'=>false, 'align'=>'center', 'index'=>'customer_group_id'));
        $this->addColumn('type', array('header'=>__('group name'),'align'=>'center', 'index'=>'customer_group_code'));
        
        $this->addColumn('action', array('header'=>__('action'),'align'=>'center',
                                         'format'=>'<a href="' . $gridUrl .'delete/id/$customer_group_id/">' . __('delete') . '</a>',
                                         'index'=>'type', 'sortable'=>false));
        $this->_initCollection();
        return parent::_beforeToHtml();
    }
    
}