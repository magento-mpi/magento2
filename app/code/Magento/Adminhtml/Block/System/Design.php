<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Adminhtml\Block\System;

class Design extends \Magento\Adminhtml\Block\Template
{
    protected function _prepareLayout()
    {
        $this->setTemplate('system/design/index.phtml');

        $this->addChild('add_new_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Add Design Change'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/new')."')",
            'class'   => 'add'
        ));

        $this->getLayout()->getBlock('page-title')->setPageTitle('Store Design Schedule');

        return parent::_prepareLayout();
    }
}
