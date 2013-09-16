<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile view item
 */
class Magento_Sales_Block_Recurring_Profile_View_Item extends Magento_Sales_Block_Recurring_Profile_View
{
    /**
     * @var Magento_Catalog_Model_Product_Option
     */
    protected $_option;
    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Catalog_Model_Product_Option $option
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Catalog_Model_Product_Option $option,
        Magento_Catalog_Model_Product $product,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        $this->_option = $option;
        $this->_product = $product;
        parent::__construct($context, $registry, $storeManager, $locale, $coreData, $data);
    }

    /**
     * Prepare item info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_shouldRenderInfo = true;
        $key = 'order_item_info';

        foreach (array(
            'name' => __('Product Name'),
            'sku' => __('SKU'),
            'qty' => __('Quantity'),
        ) as $itemKey => $label) {
            $value = $this->_profile->getInfoValue($key, $itemKey);
            if ($value) {
                $this->_addInfo(array('label' => $label, 'value' => $value,));
            }
        }

        $request = $this->_profile->getInfoValue($key, 'info_buyRequest');
        if (empty($request)) {
            return;
        }

        $request = unserialize($request);
        if (empty($request['options'])) {
            return;
        }

        $options = $this->_option->getCollection()
            ->addIdsToFilter(array_keys($request['options']))
            ->addTitleToResult($this->_profile->getInfoValue($key, 'store_id'))
            ->addValuesToResult();

        foreach ($options as $option) {
            $this->_option->setId($option->getId());

            $group = $option->groupFactory($option->getType())
                ->setOption($option)
                ->setRequest(new Magento_Object($request))
                ->setProduct($this->_product)
                ->setUseQuotePath(true)
                ->setQuoteItemOption($this->_option)
                ->validateUserValue($request['options']);

            $skipHtmlEscaping = false;
            if ('file' == $option->getType()) {
                $skipHtmlEscaping = true;

                $downloadParams = array(
                    'id'  => $this->_profile->getId(),
                    'option_id' => $option->getId(),
                    'key' => $request['options'][$option->getId()]['secret_key']
                );
                $group->setCustomOptionDownloadUrl('sales/download/downloadProfileCustomOption')
                    ->setCustomOptionUrlParams($downloadParams);
            }

            $optionValue = $group->prepareForCart();

            $this->_addInfo(array(
                'label' => $option->getTitle(),
                'value' => $group->getFormattedOptionValue($optionValue),
                'skip_html_escaping' => $skipHtmlEscaping
            ));
        }
    }
}
