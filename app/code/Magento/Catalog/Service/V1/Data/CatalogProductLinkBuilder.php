<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data;

/**
 * Builder for the CatalogProductLink Service Data Object
 *
 * @method CatalogProductLink create()
 */
class CatalogProductLinkBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(CatalogProductLink::TYPE, $type);
    }

    /**
     * Set code
     *
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(CatalogProductLink::CODE, $code);
    }
}
