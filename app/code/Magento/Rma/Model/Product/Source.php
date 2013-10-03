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
 * Source Model of Product's Attribute Enable RMA
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Product;

class Source extends \Magento\Eav\Model\Entity\Attribute\Source\Boolean
{
    /**
     * XML configuration path allow RMA on product level
     */
    const XML_PATH_PRODUCTS_ALLOWED = 'sales/magento_rma/enabled_on_product';

    /**
     * Constants - attribute value
     */
    const ATTRIBUTE_ENABLE_RMA_YES = 1;
    const ATTRIBUTE_ENABLE_RMA_NO = 0;
    const ATTRIBUTE_ENABLE_RMA_USE_CONFIG = 2;

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => __('Yes'),
                    'value' => self::ATTRIBUTE_ENABLE_RMA_YES
                ),
                array(
                    'label' => __('No'),
                    'value' => self::ATTRIBUTE_ENABLE_RMA_NO
                ),
                array(
                    'label' => __('Use config'),
                    'value' => self::ATTRIBUTE_ENABLE_RMA_USE_CONFIG
                )
            );
        }
        return $this->_options;
    }
}
