<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Enterprise
 * @package    Enterprise_GiftCard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCard_Helper_Catalog_Product_Configuration extends Magento_Core_Helper_Abstract
    implements Magento_Catalog_Helper_Product_Configuration_Interface
{
    /**
     * Catalog product configuration
     *
     * @var Magento_Catalog_Helper_Product_Configuration
     */
    protected $_catalogProductConfiguration = null;

    /**
     * @param Magento_Catalog_Helper_Product_Configuration $catalogProductConfiguration
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Configuration $catalogProductConfiguration,
        Magento_Core_Helper_Context $context
    ) {
        $this->_catalogProductConfiguration = $catalogProductConfiguration;
        parent::__construct($context);
    }

    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    public function prepareCustomOption(Magento_Catalog_Model_Product_Configuration_Item_Interface $item, $code)
    {
        $option = $item->getOptionByCode($code);
        if ($option) {
            $value = $option->getValue();
            if ($value) {
                return $this->escapeHtml($value);
            }
        }
        return false;
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    public function getGiftcardOptions(Magento_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $result = array();
        $value = $this->prepareCustomOption($item, 'giftcard_sender_name');
        if ($value) {
            $email = $this->prepareCustomOption($item, 'giftcard_sender_email');
            if ($email) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = array(
                'label' => __('Gift Card Sender'),
                'value' => $value
            );
        }

        $value = $this->prepareCustomOption($item, 'giftcard_recipient_name');
        if ($value) {
            $email = $this->prepareCustomOption($item, 'giftcard_recipient_email');
            if ($email) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = array(
                'label' => __('Gift Card Recipient'),
                'value' => $value
            );
        }

        $value = $this->prepareCustomOption($item, 'giftcard_message');
        if ($value) {
            $result[] = array(
                'label' => __('Gift Card Message'),
                'value' => $value
            );
        }

        return $result;
    }

    /**
     * Retrieves product options list
     *
     * @param Magento_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getOptions(Magento_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        return array_merge(
            $this->getGiftcardOptions($item),
            $this->_catalogProductConfiguration->getCustomOptions($item)
        );
    }
}
