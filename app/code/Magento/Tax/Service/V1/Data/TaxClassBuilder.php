<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

/**
 * Tax class data builder
 *
 * @method TaxClass create()
 */
class TaxClassBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set tax class ID.
     *
     * @param int $id
     * @return TaxClassBuilder
     */
    public function setClassId($id)
    {
        return $this->_set(TaxClass::KEY_ID, $id);
    }

    /**
     * Set tax class name.
     *
     * @param string $name
     * @return TaxClassBuilder
     */
    public function setClassName($name)
    {
        return $this->_set(TaxClass::KEY_NAME, $name);
    }

    /**
     * Set tax class type.
     *
     * @param string $type
     * @return TaxClassBuilder
     */
    public function setClassType($type)
    {
        return $this->_set(TaxClass::KEY_TYPE, $type);
    }
}
