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
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/template_collection')
            ->useOnlyActual()
            ->useSystemTemplates(false);

        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array('header'=>__('ID'), 'align'=>'center', 'index'=>'template_id'));
        $this->addColumn('code',
            array(
                'header'=>__('Template Name'),
               	'index'=>'template_code'
        ));
        
        $this->addColumn('added_at',
            array(
                'header'=>__('Added At'),
                'index'=>'added_at',
                'type'=>'datetime'
        ));
        
        $this->addColumn('modified_at',
            array(
                'header'=>__('Modified At'),
                'index'=>'modified_at',
                'type'=>'datetime'
        ));
        
        $this->addColumn('subject',
            array(
                'header'=>__('Subject'),
                'index'=>'template_subject'
        ));
        
        $this->addColumn('sender',
            array(
                'header'=>__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_sender'
        ));
        
        $this->addColumn('type',
            array(
                'header'=>__('Template Type'),
                'index'=>'template_type',
                'filter' => 'adminhtml/newsletter_template_grid_filter_type',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_type'
        ));
        
        $this->addColumn('action',
            array(
                'header'=>__('Action'),
                'index'=>'template_id',
                'sortable'=>false,
                'filter' => false,
                'width'	   => '200px',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_action'
        ));
        
        return $this;
    }
}
