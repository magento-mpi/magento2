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
        $collection = Mage::getResourceSingleton('newsletter/template_collection')
            ->useOnlyActual();
        
        $this->setCollection($collection);
    }
    
    /**
     * Configuration of grid
     */
    protected function _beforeToHtml()
    {
        $gridUrl = Mage::getUrl('adminhtml',array('controller'=>'backup'));

        $this->addColumn('id', array('header'=>__('id'), 'align'=>'center', 'index'=>'template_id',  'sortable'=>false));
        $this->addColumn('code', array('header'=>__('template code'),'align'=>'center', 'index'=>'template_code'));
        $this->addColumn('subject', array('header'=>__('template subject'),'align'=>'center', 'index'=>'template_subject'));
        $this->addColumn('sender', array('header'=>__('template sender'),'align'=>'center', 'index'=>'template_sender_email',
                                         'renderer' => new Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Sender()));
        $this->addColumn('type', array('header'=>__('template type'),'align'=>'center', 'index'=>'template_type',
                                       'renderer' => new Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Type()));
        $this->addColumn('action', array('header'=>__('template type'),'align'=>'center', 'index'=>'template_id','sortable'=>false,
                                         'renderer' => new Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action()));
       
        $this->_initCollection();
        return parent::_beforeToHtml();
    }
}