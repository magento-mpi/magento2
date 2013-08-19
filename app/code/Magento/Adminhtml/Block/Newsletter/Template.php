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
 * Adminhtml newsletter templates page content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Newsletter_Template extends Magento_Adminhtml_Block_Template
{
    protected $_template = 'newsletter/template/list.phtml';

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Newsletter_Template_Grid', 'newsletter.template.grid')
        );
        return parent::_prepareLayout();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    public function getHeaderText()
    {
        return __('Newsletter Templates');
    }
}
