<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter templates page content block
 *
 * @category   Magento
 * @package    Magento_Newsletter
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
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Magento\Newsletter\Block\Adminhtml\Template\Grid', 'newsletter.template.grid')
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
