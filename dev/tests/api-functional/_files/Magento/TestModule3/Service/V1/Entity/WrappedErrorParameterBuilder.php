<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule3\Service\V1\Entity;

class WrappedErrorParameterBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set field name.
     *
     * @param string $fieldName
     * @return $this
     */
    public function setFieldName($fieldName)
    {
        $this->_data['field_name'] = $fieldName;
        return $this;
    }

    /**
     * Set code.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->_data['code'] = $code;
        return $this;
    }

    /**
     * Set value.
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->_data['value'] = $value;
        return $this;
    }
}
