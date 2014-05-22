<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data;

/**
 * Builder for the LinkAttributeEntity Service Data Object
 *
 * @method LinkAttributeEntity create()
 */
class LinkAttributeEntityBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(LinkAttributeEntity::CODE, $code);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(LinkAttributeEntity::TYPE, $type);
    }
}
