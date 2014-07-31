<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObject;

class Option extends AbstractObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const ID = 'id';

    const LABEL = 'label';

    const TYPE = 'type';

    const USE_DEFAULT = 'use_default';

    const POSITION = 'position';

    const VALUES = 'values';

    const ATTRIBUTE_ID = 'attribute_id';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @return string
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
     * @return string|null
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * @return bool|null
     */
    public function isUseDefault()
    {
        return $this->_get(self::USE_DEFAULT);
    }

    /**
     * @return \Magento\ConfigurableProduct\Service\V1\Data\Option\Value[]
     */
    public function getValues()
    {
        return $this->_get(self::VALUES);
    }
}
