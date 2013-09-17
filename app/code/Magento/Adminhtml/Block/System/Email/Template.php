<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system templates page content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_System_Email_Template extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'system/email/template/list.phtml';

    /**
     * Create add button and grid blocks
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Add New Template'),
            'onclick'   => "window.location='" . $this->getCreateUrl() . "'",
            'class'     => 'add'
        ));

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
        return __('Transactional Emails');
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
