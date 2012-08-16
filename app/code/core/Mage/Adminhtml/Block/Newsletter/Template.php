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
 * Adminhtml newsletter templates page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Newsletter_Template extends Mage_Adminhtml_Block_Template
{
    protected $_template = 'newsletter/template/list.phtml';

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Newsletter_Template_Grid', 'newsletter.template.grid')
        );
        return parent::_prepareLayout();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Newsletter_Helper_Data')->__('Newsletter Templates');
    }
}
