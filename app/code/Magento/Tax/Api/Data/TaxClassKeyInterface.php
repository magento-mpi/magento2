<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

/**
 * previous implementation @see \Magento\Tax\Service\V1\Data\TaxClassKey
 */
interface TaxClassKeyInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_TYPE = 'type';

    const KEY_VALUE = 'value';
    /**#@-*/

    /**#@+
     * Constants defined for type of tax class key
     */
    const TYPE_ID = 'id';

    const TYPE_NAME = 'name';
    /**#@-*/

    /**
     * Get type of tax class key
     *
     * @return string
     */
    public function getType();

    /**
     * Get value of tax class key
     *
     * @return string
     */
    public function getValue();
}
