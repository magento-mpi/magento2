<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

/**
 * Builder for the ZipRange Service Data Object
 *
 * @method ZipRange create()
 */
class ZipRangeBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * Set zip range starting point
     *
     * @param int $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->_set(ZipRange::KEY_FROM, $from);
        return $this;
    }

    /**
     * Set zip range ending point
     *
     * @param int $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->_set(ZipRange::KEY_TO, $to);
        return $this;
    }
}
