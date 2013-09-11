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
 * admin edit tabs for grouped product
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs;

class Grouped extends \Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('super', array(
            'label'     => __('Associated Products'),
            'url'       => $this->getUrl('*/*/superGroup', array('_current'=>true)),
            'class'     => 'ajax',
        ));
    }
}
