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
 * Create product attribute set selector
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit;

class AttributeSet extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get options for suggest widget
     *
     * @return array
     */
    public function getSelectorOptions()
    {
        return array(
            'source' => $this->getUrl('*/catalog_product/suggestProductTemplates'),
            'className' => 'category-select',
            'showRecent' => true,
            'storageKey' => 'product-template-key',
            'minLength' => 0,
            'currentlySelected' => $this->_coreRegistry->registry('product')->getAttributeSetId(),
        );
    }
}
