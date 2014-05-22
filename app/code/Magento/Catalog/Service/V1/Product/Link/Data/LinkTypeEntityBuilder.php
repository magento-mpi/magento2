<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data;

/**
 * Builder for the LinkTypeEntity Service Data Object
 *
 * @method LinkTypeEntity create()
 */
class LinkTypeEntityBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(LinkTypeEntity::TYPE, $type);
    }

    /**
     * Set code
     *
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(LinkTypeEntity::CODE, $code);
    }
}
