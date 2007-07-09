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

        $this->addColumn('id',
            array('header'=>__('id'), 'align'=>'center', 'index'=>'template_id',  'sortable'=>false));
        $this->addColumn('code',
            array(
                'header'=>__('Template Code'),
                'align'=>'center',
                'index'=>'template_code'
        ));
        $this->addColumn('subject',
            array(
                'header'=>__('Subject'),
                'align'=>'center',
                'index'=>'template_subject'
        ));
        $this->addColumn('sender',
            array(
                'header'=>__('Sender'),
                'align'=>'center',
                'index'=>'template_sender_email',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_sender'
        ));
        $this->addColumn('type',
            array(
                'header'=>__('Template Type'),
                'align'=>'center',
                'index'=>'template_type',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_type'
        ));
        $this->addColumn('action',
            array(
                'header'=>__('Action'),
                'align'=>'center',
                'index'=>'template_id',
                'sortable'=>false,
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_action'
        ));

        $this->_initCollection();
        return parent::_beforeToHtml();
    }
}