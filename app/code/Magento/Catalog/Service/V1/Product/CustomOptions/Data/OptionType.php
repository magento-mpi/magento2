<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data;

class OptionType extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    const LABEL = 'label';
    const CODE = 'code';
    const GROUP = 'group';

    /**
     * Get option type label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * Get option type code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Get option type group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->_get(self::GROUP);
    }
}
