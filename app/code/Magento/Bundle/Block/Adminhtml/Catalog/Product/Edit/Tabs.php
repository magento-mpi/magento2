<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit;

/**
 * Adminhtml product edit tabs
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs
{
    /**
     * @var string
     */
    protected $_attributeTabBlock = 'Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes';

    /**
     * @return $this|void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('bundle_items', array(
            'label'     => __('Bundle Items'),
            'url'   => $this->getUrl('adminhtml/*/bundles', array('_current' => true)),
            'class' => 'ajax',
        ));
        $this->bindShadowTabs('bundle_items', 'customer_options');
    }
}
