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
 * Catalog bundle product info block
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Block_Catalog_Product_View_Type_Bundle extends Magento_Catalog_Block_Product_View_Abstract
{
    protected $_optionRenderers = array();
    protected $_options         = null;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Catalog product
     *
     * @var Magento_Catalog_Helper_Product
     */
    protected $_catalogProduct = null;

    /**
     * @param Magento_Catalog_Helper_Product $catalogProduct
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Product $catalogProduct,
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_catalogProduct = $catalogProduct;
        parent::__construct($eventManager, $taxData, $catalogData, $coreData, $context, $data);
    }

    public function getOptions()
    {
        if (!$this->_options) {
            $product = $this->getProduct();
            $typeInstance = $product->getTypeInstance();
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $typeInstance->getOptionsCollection($product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($product),
                $product
            );

            $this->_options = $optionCollection->appendSelections($selectionCollection, false,
                $this->_catalogProduct->getSkipSaleableCheck()
            );
        }

        return $this->_options;
    }

    public function hasOptions()
    {
        $this->getOptions();
        if (empty($this->_options) || !$this->getProduct()->isSalable()) {
            return false;
        }
        return true;
    }

    /**
     * Returns JSON encoded config to be used in JS scripts
     *
     * @return string
     */
    public function getJsonConfig()
    {
        Mage::app()->getLocale()->getJsPriceFormat();
        $optionsArray = $this->getOptions();
        $options      = array();
        $selected     = array();
        $currentProduct = $this->getProduct();
        /* @var $coreHelper Magento_Core_Helper_Data */
        $coreHelper   = $this->_coreData;
        /* @var $catalogHelper Magento_Catalog_Helper_Data */
        $catalogHelper = $this->_catalogData;
        /* @var $taxHelper Magento_Tax_Helper_Data */
        $taxHelper = $this->_taxData;
        /* @var $bundlePriceModel Magento_Bundle_Model_Product_Price */
        $bundlePriceModel = Mage::getModel('Magento_Bundle_Model_Product_Price');

        if ($preConfiguredFlag = $currentProduct->hasPreconfiguredValues()) {
            $preConfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }

        $position = 0;
        foreach ($optionsArray as $_option) {
            /* @var $_option Magento_Bundle_Model_Option */
            if (!$_option->getSelections()) {
                continue;
            }

            $optionId = $_option->getId();
            $option = array (
                'selections' => array(),
                'title'      => $_option->getTitle(),
                'isMulti'    => in_array($_option->getType(), array('multi', 'checkbox')),
                'position'   => $position++
            );

            $selectionCount = count($_option->getSelections());

            foreach ($_option->getSelections() as $_selection) {
                /* @var $_selection Magento_Catalog_Model_Product */
                $selectionId = $_selection->getSelectionId();
                $_qty = !($_selection->getSelectionQty() * 1) ? '1' : $_selection->getSelectionQty() * 1;
                // recalculate currency
                $tierPrices = $_selection->getTierPrice();
                foreach ($tierPrices as &$tierPriceInfo) {
                    $tierPriceInfo['price'] = $coreHelper->currency($tierPriceInfo['price'], false, false);
                }
                unset($tierPriceInfo); // break the reference with the last element

                $itemPrice = $bundlePriceModel->getSelectionFinalTotalPrice($currentProduct, $_selection,
                    $currentProduct->getQty(), $_selection->getQty(), false, false
                );

                $canApplyMAP = false;

                $_priceInclTax = $taxHelper->getPrice($_selection, $itemPrice, true);
                $_priceExclTax = $taxHelper->getPrice($_selection, $itemPrice);

                if ($currentProduct->getPriceType() == Magento_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
                    $_priceInclTax = $taxHelper->getPrice($currentProduct, $itemPrice, true);
                    $_priceExclTax = $taxHelper->getPrice($currentProduct, $itemPrice);
                }

                $selection = array (
                    'qty'              => $_qty,
                    'customQty'        => $_selection->getSelectionCanChangeQty(),
                    'price'            => $coreHelper->currency($_selection->getFinalPrice(), false, false),
                    'priceInclTax'     => $coreHelper->currency($_priceInclTax, false, false),
                    'priceExclTax'     => $coreHelper->currency($_priceExclTax, false, false),
                    'priceValue'       => $coreHelper->currency($_selection->getSelectionPriceValue(), false, false),
                    'priceType'        => $_selection->getSelectionPriceType(),
                    'tierPrice'        => $tierPrices,
                    'name'             => $_selection->getName(),
                    'plusDisposition'  => 0,
                    'minusDisposition' => 0,
                    'canApplyMAP'      => $canApplyMAP
                );

                $responseObject = new Magento_Object();
                $args = array('response_object' => $responseObject, 'selection' => $_selection);
                $this->_eventManager->dispatch('bundle_product_view_config', $args);
                if (is_array($responseObject->getAdditionalOptions())) {
                    foreach ($responseObject->getAdditionalOptions() as $o => $v) {
                        $selection[$o] = $v;
                    }
                }
                $option['selections'][$selectionId] = $selection;

                if (($_selection->getIsDefault() || ($selectionCount == 1 && $_option->getRequired()))
                    && $_selection->isSalable()
                ) {
                    $selected[$optionId][] = $selectionId;
                }
            }
            $options[$optionId] = $option;

            // Add attribute default value (if set)
            if ($preConfiguredFlag) {
                $configValue = $preConfiguredValues->getData('bundle_option/' . $optionId);
                if ($configValue) {
                    $defaultValues[$optionId] = $configValue;
                }
            }
        }

        $config = array(
            'options'       => $options,
            'selected'      => $selected,
            'bundleId'      => $currentProduct->getId(),
            'priceFormat'   => Mage::app()->getLocale()->getJsPriceFormat(),
            'basePrice'     => $coreHelper->currency($currentProduct->getPrice(), false, false),
            'priceType'     => $currentProduct->getPriceType(),
            'specialPrice'  => $currentProduct->getSpecialPrice(),
            'includeTax'    => $taxHelper->priceIncludesTax() ? 'true' : 'false',
            'isFixedPrice'  => $this->getProduct()->getPriceType() == Magento_Bundle_Model_Product_Price::PRICE_TYPE_FIXED,
            'isMAPAppliedDirectly' => $catalogHelper->canApplyMsrp($this->getProduct(), null, false)
        );

        if ($preConfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        return $coreHelper->jsonEncode($config);
    }

    public function addRenderer($type, $block)
    {
        $this->_optionRenderers[$type] = $block;
    }

    public function getOptionHtml($option)
    {
        if (!isset($this->_optionRenderers[$option->getType()])) {
            return __('There is no defined renderer for "%1" option type.', $option->getType());
        }
        return $this->getLayout()->createBlock($this->_optionRenderers[$option->getType()])
            ->setOption($option)->toHtml();
    }
}
