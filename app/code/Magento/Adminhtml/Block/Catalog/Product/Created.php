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
 * Product after creation popup window
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product;

class Created extends \Magento\Adminhtml\Block\Widget
{
    protected $_configurableProduct;
    protected $_product;

    /**
     * @var string
     */
    protected $_template = 'catalog/product/created.phtml';

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild('close_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'   => __('Close Window'),
            'onclick' => 'addProduct(true)'
        ));
    }


    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    public function getProductId()
    {
        return (int) $this->getRequest()->getParam('id');
    }

    /**
     * Identifies edit mode of popup
     *
     * @return boolean
     */
    public function isEdit()
    {
        return (bool)$this->getRequest()->getParam('edit');
    }

    /**
     * Retrieve serialized json with configurable attributes values of simple
     *
     * @return string
     */
    public function getAttributesJson()
    {
        $result = array();
        foreach ($this->getAttributes() as $attribute) {
            $value = $this->getProduct()->getAttributeText($attribute->getAttributeCode());

            $result[] = array(
                'label'         => $value,
                'value_index'   => $this->getProduct()->getData($attribute->getAttributeCode()),
                'attribute_id'  => $attribute->getId()
            );
        }

        return $this->_coreData->jsonEncode($result);
    }

    /**
     * Retrieve array of attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $configurableProduct = $this->getConfigurableProduct();
        if ($configurableProduct->getId()) {
            return $configurableProduct
                ->getTypeInstance()
                ->getUsedProductAttributes($configurableProduct);
        }

        $attributes = array();
        $attributesIds = $this->getRequest()->getParam('required');
        if ($attributesIds) {
            $product = $this->getProduct();
            $typeInstance = $product->getTypeInstance();
            foreach (explode(',', $attributesIds) as $attributeId) {
                $attribute = $typeInstance->getAttributeById($attributeId, $product);
                if ($attribute) {
                    $attributes[] = $attribute;
                }
            }
        }
        return $attributes;
    }

    /**
     * Retrieve configurable product for created/edited simple
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getConfigurableProduct()
    {
        if ($this->_configurableProduct === null) {
            $this->_configurableProduct = $this->_productFactory->create()->setStore(0)
                ->load($this->getRequest()->getParam('product'));
        }
        return $this->_configurableProduct;
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if ($this->_product === null) {
            $this->_product = $this->_productFactory->create()->setStore(0)
                ->load($this->getRequest()->getParam('id'));
        }
        return $this->_product;
    }
}
