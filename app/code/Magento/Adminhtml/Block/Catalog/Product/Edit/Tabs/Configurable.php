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
 * admin edit tabs for configurable products
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs;

class Configurable extends \Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs
{
    /**
     * Preparing layout
     *
     * @return \Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs\Configurable
     */
    protected function _prepareLayout()
    {
        $this->addTab('super_settings', array(
            'label'     => __('Configurable Product Settings'),
            'content'   => $this->getLayout()
                ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Settings')
                ->toHtml(),
            'active'    => true
        ));
    }
}
