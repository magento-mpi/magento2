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
class TaxClassBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set tax class ID.
     *
     * @param int $id
     * @return TaxClassBuilder
     */
    public function setId($id)
    {
        $this->_data[TaxClass::KEY_ID] = $id;
        return $this;
    }

    /**
     * Set tax class name.
     *
     * @param string $name
     * @return TaxClassBuilder
     */
    public function setName($name)
    {
        $this->_data[TaxClass::KEY_NAME] = $name;
        return $this;
    }

    /**
     * Set tax class type.
     *
     * @param string $type
     * @return TaxClassBuilder
     */
    public function setType($type)
    {
        $this->_data[TaxClass::KEY_TYPE] = $type;
        return $this;
    }
}
