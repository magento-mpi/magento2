<?php
/**
 * Adminhtml cms grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cmsGrid');
    }

    protected function _initCollection()
    {
        $pageCollection = Mage::getResourceModel('cms/page_collection');
        $this->setCollection($pageCollection);
    }

    protected function _beforeToHtml()
    {
        $this->addColumn('page_title', array('header'=>__('title'), 'align'=>'left', 'index'=>'page_title'));
        $this->addColumn('page_identifier', array('header'=>__('identifier'), 'align'=>'left', 'index'=>'page_identifier'));
        $this->addColumn('page_creation_time', array('header'=>__('creation time'), 'index'=>'page_creation_time'));
        $this->addColumn('page_update_time', array('header'=>__('update time'), 'index'=>'page_update_time'));
        $this->addColumn('page_order', array('header'=>__('order'), 'width'=>5, 'index'=>'page_order'));

        $this->_initCollection();
        return parent::_beforeToHtml();
    }
}