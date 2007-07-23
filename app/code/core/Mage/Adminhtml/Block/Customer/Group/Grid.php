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

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGroupGrid');
    }

    /**
     * Init customer groups collection
     * @return void
     */
    protected function _initCollection()
    {
        $this->setCollection(Mage::getResourceSingleton('customer/group_collection'));
    }

    /**
     * Configuration of grid
     */
    protected function _beforeToHtml()
    {
        $this->addColumn('time', array(
            'header' => __('ID'),
            'index' => 'customer_group_id',
        ));
        $this->addColumn('type', array(
            'header' => __('Group Name'),
            'index' => 'customer_group_code',
        ));
       
        $this->_initCollection();
        return parent::_beforeToHtml();
    }
    
     
    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}