<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data;

class ConfigurableAttribute extends \Magento\Framework\Service\Data\Eav\AbstractObject {

    const ID = 'id';
    const LABEL = 'label';
    const USE_DEFAULT = 'use_default';
    const POSITION = 'position';
    const VALUES = 'values';
    const ATTRIBUTE_ID = 'attribute_id';

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
        return $this->_get(self::ATTRIBUTE_ID);
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * @return bool|null
     */
    public function isUseDefault()
    {
        return $this->_get(self::USE_DEFAULT);
    }

    /**
     * @return \Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute\Value[]
     */
    public function getValues()
    {
        return $this->_get(self::VALUES);
    }
}