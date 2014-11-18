<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

/**
 * @see \Magento\Tax\Service\V1\Data\TaxRateTitle
 */
interface TaxRateTitleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     *
     * Tax rate field key.
     */
    const KEY_STORE_ID = 'store_id';

    const KEY_VALUE_ID = 'value';
    /**#@-*/

    /**
     * Get store id
     *
     * @return string
     */
    public function getStoreId();

    /**
     * Get title value
     *
     * @return string
     */
    public function getValue();
}
