<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml  system templates grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Email_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setEmptyText(Mage::helper('Mage_Adminhtml_Helper_Data')->__('No Templates Found'));
        $this->setId('systemEmailTemplateGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('Mage_Core_Model_Resource_Email_Template_Collection');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('template_id',
            array(
                  'header'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('ID'),
                  'index'=>'template_id'
            )
        );

        $this->addColumn('code',
            array(
                'header'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Template Name'),
                'index'=>'template_code'
        ));

        $this->addColumn('added_at',
            array(
                'header'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Date Added'),
                'index'=>'added_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('modified_at',
            array(
                'header'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Date Updated'),
                'index'=>'modified_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('subject',
            array(
                'header'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Subject'),
                'index'=>'template_subject'
        ));
        /*
        $this->addColumn('sender',
            array(
                'header'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'Mage_Adminhtml_Block_System_Email_Template_Grid_Renderer_Sender'
        ));
        */
        $this->addColumn('type',
            array(
                'header'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Template Type'),
                'index'=>'template_type',
                'filter' => 'Mage_Adminhtml_Block_System_Email_Template_Grid_Filter_Type',
                'renderer' => 'Mage_Adminhtml_Block_System_Email_Template_Grid_Renderer_Type'
        ));

        $this->addColumn('action',
            array(
                'header'	=> Mage::helper('Mage_Adminhtml_Helper_Data')->__('Action'),
                'index'		=> 'template_id',
                'sortable'  => false,
                'filter' 	=> false,
                'width'		=> '100px',
                'renderer'  => 'Mage_Adminhtml_Block_System_Email_Template_Grid_Renderer_Action'
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}

