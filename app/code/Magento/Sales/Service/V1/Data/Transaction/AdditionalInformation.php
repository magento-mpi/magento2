<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Sales\Service\V1\Data\Transaction;

use Magento\Framework\Api\AbstractSimpleObject as DataObject;

/**
 * @codeCoverageIgnore
 */
class AdditionalInformation extends DataObject
{
    /**#@+
     * Data object properties
     * @var string
     */
    const KEY = 'key';
    const VALUE = 'value';
    /**#@-*/

    /**
     * Returns key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_get(self::KEY);
    }

    /**
     * Returns value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
