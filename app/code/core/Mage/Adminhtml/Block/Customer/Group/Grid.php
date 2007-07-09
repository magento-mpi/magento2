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
        $this->setId('customerGrid');
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
        $gridUrl = Mage::getUrl('adminhtml',array('controller'=>'customer_group'));

        $this->addColumn('time', array(
            'header' => __('id'),
            'sortable' => false,
            'align' => 'center',
            'index' => 'customer_group_id',
        ));
        $this->addColumn('type', array(
            'header' => __('Group Name'),
            'align' => 'center',
            'index' => 'customer_group_code',
        ));
        $this->addColumn('action', array(
            'header' => __('Action'),
            'align' => 'center',
            'format' => '<a href="' . $gridUrl .'edit/id/$customer_group_id/" class="edit-url">' . __('edit') . '</a> | '
                .  '<a href="' . $gridUrl .'delete/id/$customer_group_id/">' . __('delete') . '</a>',
            'index' => 'type',
            'sortable' => false,
        ));
        $this->_initCollection();
        return parent::_beforeToHtml();
    }

}