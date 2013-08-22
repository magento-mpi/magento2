<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml pending tags grid block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Pending extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'tag/index.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('tagsGrid', 'Magento_Tag_Block_Adminhtml_Grid_Pending');
        return parent::_prepareLayout();
    }

    public function getCreateButtonHtml()
    {
        return '';
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('tagsGrid');
    }

    public function getHeaderHtml()
    {
        return __('Pending Tags');
    }
}
