<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data;

/**
 * Builder for the LinkAttribute Service Data Object
 *
 * @method LinkAttribute create()
 */
class LinkAttributeBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(LinkAttribute::CODE, $code);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(LinkAttribute::TYPE, $type);
    }
}
