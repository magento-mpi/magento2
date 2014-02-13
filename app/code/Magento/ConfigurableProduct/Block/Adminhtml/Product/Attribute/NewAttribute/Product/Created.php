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
 * New product attribute created on product edit page
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Attribute\NewAttribute\Product;

class Created extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/attribute/new/created.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $_setFactory;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_setFactory = $setFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve list of product attributes
     *
     * @return array
     */
    protected function _getGroupAttributes()
    {
        $attributes = array();
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_coreRegistry->registry('product');
        foreach ($product->getAttributes($this->getRequest()->getParam('group')) as $attribute) {
            /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
            if ($attribute->getId() == $this->getRequest()->getParam('attribute')) {
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }

    /**
     * Retrieve HTML for 'Close' button
     *
     * @return string
     */
    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    /**
     * Retrieve attributes data as JSON
     *
     * @return string
     */
    public function getAttributesBlockJson()
    {
        $result = array();
        if ($this->getRequest()->getParam('product_tab') == 'variations') {
            /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
            $attribute =
                $this->_attributeFactory->create()->load($this->getRequest()->getParam('attribute'));
            $result = array(
                'tab' => $this->getRequest()->getParam('product_tab'),
                'attribute' => array(
                    'id' => $attribute->getId(),
                    'label' => $attribute->getFrontendLabel(),
                    'code' => $attribute->getAttributeCode(),
                    'options' => $attribute->getSourceModel() ? $attribute->getSource()->getAllOptions(false) : array()
                )
            );
        }
        $newAttributeSetId = $this->getRequest()->getParam('new_attribute_set_id');
        if ($newAttributeSetId) {
            /** @var $attributeSet \Magento\Eav\Model\Entity\Attribute\Set */
            $attributeSet = $this->_setFactory->create()->load($newAttributeSetId);
            $result['set'] = array(
                'id' => $attributeSet->getId(),
                'label' => $attributeSet->getAttributeSetName(),
            );
        }

        return $this->_jsonEncoder->encode($result);
    }
}
