<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter templates page content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Block\Adminhtml;

class Template extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'template/list.phtml';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Add New Template'),
                'onclick' => "window.location='" . $this->getCreateUrl() . "'",
                'class' => 'add primary add-template'
            )
        );

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Magento\Newsletter\Block\Adminhtml\Template\Grid',
                'newsletter.template.grid'
            )
        );
        return parent::_prepareLayout();
    }

    /**
     * Get the url for create
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Newsletter Templates');
    }
}
