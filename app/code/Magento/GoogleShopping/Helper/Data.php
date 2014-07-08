<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Helper;

use Magento\Framework\Gdata\Gshopping\Extension\Tax;
use Magento\Tax\Service\V1\Data\TaxRate;
use Magento\Tax\Service\V1\Data\TaxRule;

/**
 * Google Content Data Helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\String
     */
    protected $string;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * filterBuilder
     *
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * TaxRuleService
     *
     * @var \Magento\Tax\Service\V1\TaxRuleServiceInterface
     */
    protected $_taxRuleService;

    /**
     * TaxRateService
     *
     * @var \Magento\Tax\Service\V1\TaxRateServiceInterface
     */
    protected $_taxRateService;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Tax\Service\V1\TaxRuleServiceInterface $taxRuleService
     * @param \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\String $string,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Tax\Service\V1\TaxRuleServiceInterface $taxRuleService,
        \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService
    ) {
        $this->string = $string;
        $this->_storeManager = $storeManager;
        $this->_filterBuilder = $filterBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_taxRuleService = $taxRuleService;
        $this->_taxRateService = $taxRateService;
        parent::__construct($context);
    }

    /**
     * Get Google Content Product ID
     *
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    public function buildContentProductId($productId, $storeId)
    {
        return $productId . '_' . $storeId;
    }

    /**
     * Remove characters and words not allowed by Google Content in title and content (description).
     *
     * To avoid "Expected response code 200, got 400.
     * Reason: There is a problem with the character encoding of this attribute"
     *
     * @param string $string
     * @return string
     */
    public function cleanAtomAttribute($string)
    {
        return $this->string->substr(preg_replace('/[\pC¢€•—™°½]|shipping/ui', '', $string), 0, 3500);
    }

    /**
     * Normalize attribute's name.
     * The name has to be in lower case and the words are separated by symbol "_".
     * For instance: Meta Description = meta_description
     *
     * @param string $name
     * @return string
     */
    public function normalizeName($name)
    {
        return strtolower(preg_replace('/[\s_]+/', '_', $name));
    }

    /**
     * Parse \Exception Response Body
     *
     * @param string $message \Exception message to parse
     * @param null|\Magento\Catalog\Model\Product $product
     * @return string
     */
    public function parseGdataExceptionMessage($message, $product = null)
    {
        $result = array();
        foreach (explode("\n", $message) as $row) {
            if (trim($row) == '') {
                continue;
            }

            if (strip_tags($row) == $row) {
                $row = preg_replace('/@ (.*)/', __("See '\\1'"), $row);
                if (!is_null($product)) {
                    $row .= ' ' . __(
                        "for product '%1' (in '%2' store)",
                        $product->getName(),
                        $this->_storeManager->getStore($product->getStoreId())->getName()
                    );
                }
                $result[] = $row;
                continue;
            }

            // parse not well-formatted xml
            preg_match_all('/(reason|field|type)=\"([^\"]+)\"/', $row, $matches);

            if (is_array($matches) && count($matches) == 3) {
                if (is_array($matches[1]) && count($matches[1]) > 0) {
                    $c = count($matches[1]);
                    for ($i = 0; $i < $c; $i++) {
                        if (isset($matches[2][$i])) {
                            $result[] = ucfirst($matches[1][$i]) . ': ' . $matches[2][$i];
                        }
                    }
                }
            }
        }
        return implode(". ", $result);
    }

    /**
     * Get rates by customer and product classes
     *
     * @param int $customerTaxClassId
     * @param int $productTaxClassId
     * @return TaxRate[]
     */
    public function getRatesByCustomerAndProductTaxClassId($customerTaxClassId, $productTaxClassId)
    {
        $filterGroups = [
            [
                $this->_filterBuilder
                    ->setField(TaxRule::CUSTOMER_TAX_CLASS_IDS)
                    ->setValue([$customerTaxClassId])
                    ->create(),
            ],
            [
                $this->_filterBuilder
                    ->setField(TaxRule::PRODUCT_TAX_CLASS_IDS)
                    ->setValue([$productTaxClassId])
                    ->create(),
            ],
        ];

        foreach ($filterGroups as $filterGroup) {
            $this->_searchCriteriaBuilder->addFilter($filterGroup);
        }

        $searchResults = $this->_taxRuleService->searchTaxRules(
            $this->_searchCriteriaBuilder->create()
        );

        $taxRules = $searchResults->getItems();
        $rates = [];
        foreach ($taxRules as $taxRule) {
            $rateIds = $taxRule->getTaxRateIds();
            if (!empty($rateIds)) {
                foreach ($rateIds as $rateId) {
                    $rates[] = $this->_taxRateService->getTaxRate($rateId);
                }
            }
        }
        return $rates;
    }
}
