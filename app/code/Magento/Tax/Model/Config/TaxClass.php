<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tax\Model\Config;

/**
 * TaxClass Config
 */
class TaxClass extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Core\Model\Resource\Config
     */
    protected $resourceConfig;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Core\Model\Resource\Config $resourceConfig
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Core\Model\Resource\Config $resourceConfig,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->attributeFactory = $attributeFactory;
        $this->taxHelper = $taxHelper;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Update the default product tax class
     *
     * @return \Magento\Tax\Model\Config\TaxClass
     */
    protected function _afterSave()
    {
        // Get the default tax class for product
        $defaultProductTaxClass = $this->taxHelper->getDefaultProductTaxClass();
        $attributeCode = "tax_class_id";

        $attribute = $this->attributeFactory->create();
        $attribute->loadByCode(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
        if (!$attribute->getId()) {
            throw new \Magento\Framework\Model\Exception(__('Invalid attribute %1', $attributeCode));
        }
        $attribute->setData("default_value", $defaultProductTaxClass);
        $attribute->save();

        return parent::_afterSave($this);
    }
}
