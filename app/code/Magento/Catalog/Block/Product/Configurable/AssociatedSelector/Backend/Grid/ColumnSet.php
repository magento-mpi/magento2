<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block representing set of columns in product grid
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
namespace Magento\Catalog\Block\Product\Configurable\AssociatedSelector\Backend\Grid;

class ColumnSet
    extends \Magento\Backend\Block\Widget\Grid\ColumnSet
{
    /**
     * Registry instance
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * Product type configurable instance
     *
     * @var \Magento\Catalog\Model\Product\Type\Configurable
     */
    protected $_productType;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory $generatorFactory
     * @param Magento_Backend_Model_Widget_Grid_SubTotals $subtotals
     * @param Magento_Backend_Model_Widget_Grid_Totals $totals
     * @param Magento_Catalog_Model_Product_Type_Configurable $productType
     * @param Magento_Core_Model_Registry $registryManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory $generatorFactory,
        Magento_Backend_Model_Widget_Grid_SubTotals $subtotals,
        Magento_Backend_Model_Widget_Grid_Totals $totals,
        Magento_Catalog_Model_Product_Type_Configurable $productType,
        Magento_Core_Model_Registry $registryManager,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $generatorFactory, $subtotals, $totals, $data);

        $this->_registryManager = $registryManager;
        $this->_productType = $productType;
    }

    /**
     * Retrieve currently edited product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_registryManager->registry('current_product');
    }

    /**
     * Preparing layout
     *
     * @return \Magento\Catalog\Block\Product\Configurable\AssociatedSelector\Backend\Grid\ColumnSet
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $product = $this->getProduct();
        $attributes = $this->_productType->getUsedProductAttributes($product);
        foreach ($attributes as $attribute) {
            /** @var $attribute \Magento\Catalog\Model\Entity\Attribute */
            /** @var $block \Magento\Backend\Block\Widget\Grid\Column */
            $block = $this->addChild(
                $attribute->getAttributeCode(),
                'Magento\Backend\Block\Widget\Grid\Column',
                array(
                    'header' => $attribute->getStoreLabel(),
                    'index' => $attribute->getAttributeCode(),
                    'type' => 'options',
                    'options' => $this->getOptions($attribute->getSource()),
                    'sortable' => false
                )
            );
            $block->setId($attribute->getAttributeCode())->setGrid($this);
        }
        return $this;
    }

    /**
     * Get option as hash
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource $sourceModel
     * @return array
     */
    private function getOptions(\Magento\Eav\Model\Entity\Attribute\Source\AbstractSource $sourceModel)
    {
        $result = array();
        foreach ($sourceModel->getAllOptions() as $option) {
            if ($option['value'] != '') {
                $result[$option['value']] = $option['label'];
            }
        }
        return $result;
    }
}
