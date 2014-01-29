<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system templates page content block
 *
 * @category   Magento
 * @package    Magento_Email
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Email\Block\Adminhtml;

class Template extends \Magento\Backend\Block\Template
{

    /**
     * Template list
     *
     * @var string
     */
    protected $_template = 'template/list.phtml';

    /**
     * Create add button and grid blocks
     *
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Magento\Backend\Block\Widget\Button', array(
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
        return $this->getUrl('adminhtml/*/new');
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
