<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml product edit tabs
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit;

class Tabs extends \Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs
{
    protected $_attributeTabBlock = '\Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes';

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('bundle_items', array(
            'label'     => __('Bundle Items'),
            'url'   => $this->getUrl('*/*/bundles', array('_current' => true)),
            'class' => 'ajax',
        ));
        $this->bindShadowTabs('bundle_items', 'customer_options');
    }
}
