<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Sales\Service\V1\Data\Transaction;

use Magento\Framework\Service\Data\AbstractObject as DataObject;

class AdditionalInformation extends DataObject
{
    const KEY = 'key';
    const VALUE = 'value';

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
