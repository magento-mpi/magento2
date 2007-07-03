<?php
/**
 * Adminhtml newsletter templates grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Newsletter_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _initCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/template_collection');
        $this->setCollection($collection);
    }
    
    /**
     * Configuration of grid
     */
    protected function _beforeToHtml()
    {
        $gridUrl = Mage::getUrl('adminhtml',array('controller'=>'backup'));
        $this->setPagerVisibility(false);
        $this->addColumn('id', array('header'=>__('id'), 'align'=>'center', 'index'=>'template_id',  'sortable'=>false));
        $this->addColumn('code', array('header'=>__('template code'),'align'=>'center', 'index'=>'template_code'));
        // TODO: Write a configuration details tommorow
        $this->_initCollection();
        return parent::_beforeToHtml();
    }
}