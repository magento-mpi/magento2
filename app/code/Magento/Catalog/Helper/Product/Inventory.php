<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Helper\Product;

use \Magento\CatalogInventory\Service\V1\Data\StockItem;
use Magento\Framework\App\Helper\Context;
use \Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Catalog Product Inventory Helper
 */
class Inventory extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @param string $field
     * @param StockItem $dataObject
     * @return mixed
     */
    public function getFieldValue($field, StockItem $dataObject)
    {
        if ($dataObject->getStockId()) {
            return $this->getDoFieldData($field, $dataObject);
        }

        return $this->getDefaultConfigValue($field);
    }

    /**
     * @param string $field
     * @param StockItem $dataObject
     * @return mixed|null|string
     */
    public function getConfigFieldValue($field, StockItem $dataObject)
    {
        if ($dataObject->getStockId()) {
            if ($this->getDoFieldData('use_config_' . $field, $dataObject) == 0) {
                return $this->getDoFieldData($field, $dataObject);
            }
        }

        return $this->getDefaultConfigValue($field);
    }

    /**
     * @param string $field
     * @return string|null
     */
    public function getDefaultConfigValue($field)
    {
        return $this->scopeConfig->getValue(
            \Magento\CatalogInventory\Model\Stock\Item::XML_PATH_ITEM . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $field
     * @param StockItem $dataObject
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function getDoFieldData($field, StockItem $dataObject)
    {
        $possibleMethods = array(
            'get' . \Magento\Framework\Api\SimpleDataObjectConverter::snakeCaseToUpperCamelCase($field),
            'is' . \Magento\Framework\Api\SimpleDataObjectConverter::snakeCaseToUpperCamelCase($field)
        );

        foreach ($possibleMethods as $method) {
            if (method_exists($dataObject, $method)) {
                return $dataObject->{$method}();
            }
        }
        throw new \BadMethodCallException(__('Field "%1" was not found in DO "%2".', $field, get_class($dataObject)));
    }
}
