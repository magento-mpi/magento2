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
 * Admin tax class product toolbar
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Tax\Rate\Toolbar;

class Add extends \Magento\Adminhtml\Block\Template
{

    protected $_template = 'tax/toolbar/rate/add.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('addButton', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label' => __('Add New Tax Rate'),
            'onclick' => 'window.location.href=\''.$this->getUrl('*/tax_rate/add').'\'',
            'class' => 'add'
        ));
        return parent::_prepareLayout();
    }
}
