<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data;

class OptionValue extends \Magento\Framework\Service\Data\AbstractObject
{
    const CODE = 'code';
    const VALUE = 'value';

    /**
     * Get option value code
     *
     * @return string
     */
    public function getId()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
