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
 * Adminhtml system templates page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Email_Template extends Mage_Adminhtml_Block_Template
{

    protected $_template = 'system/email/template/list.phtml';

    /**
     * Create add button and grid blocks
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add New Template'),
                    'onclick'   => "window.location='" . $this->getCreateUrl() . "'",
                    'class'     => 'add'
        )));
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Email_Template_Grid', 'email.template.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     * Get URL for create new email template
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * Get transactional emails page header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Mage_Adminhtml_Helper_Data')->__('Transactional Emails');
    }

    /**
     * Get Add New Template button html
     *
     * @return string
     */
    protected function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}
