<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data;

/**
 * Builder for the LinkType Service Data Object
 *
 * @method LinkType create()
 * @codeCoverageIgnore
 */
class LinkTypeBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(LinkType::TYPE, $type);
    }

    /**
     * Set code
     *
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(LinkType::CODE, $code);
    }
}
