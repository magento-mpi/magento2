<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tax class product toolbar
 *
 * @category   Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Tax\Block\Adminhtml\Rate\Toolbar;

class Add extends \Magento\Backend\Block\Template
{

    /**
     * @var string
     */
    protected $_template = 'toolbar/rate/add.phtml';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild('addButton', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Add New Tax Rate'),
            'onclick' => 'window.location.href=\''.$this->getUrl('tax/rate/add').'\'',
            'class' => 'add'
        ));
        return parent::_prepareLayout();
    }
}
