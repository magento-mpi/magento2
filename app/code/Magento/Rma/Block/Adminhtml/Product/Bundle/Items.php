<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional Renderer of Product's Attribute Enable RMA control structure
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Product\Bundle;

class Items extends \Magento\Core\Block\Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        $this->setItems($this->_coreRegistry->registry('current_rma_bundle_item'));
        $this->setParentId((int)$this->getRequest()->getParam('item_id'));
    }
}
