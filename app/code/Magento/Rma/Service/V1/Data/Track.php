<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

use Magento\Framework\Api\AbstractExtensibleObject as DataObject;

class Track extends DataObject
{
    /**#@+
     * Data object properties
     */
    const ENTITY_ID = 'entity_id';
    const RMA_ENTITY_ID = 'rma_entity_id';
    const TRACK_NUMBER = 'track_number';
    const CARRIER_TITLE = 'carrier_title';
    const CARRIER_CODE = 'carrier_code';
    /**#@-*/

    /**
     * Returns entity id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Returns rma entity id
     *
     * @return int
     */
    public function getRmaEntityId()
    {
        return $this->_get(self::RMA_ENTITY_ID);
    }

    /**
     * Returns track number
     *
     * @return string
     */
    public function getTrackNumber()
    {
        return $this->_get(self::TRACK_NUMBER);
    }

    /**
     * Returns carrier title
     *
     * @return string
     */
    public function getCarrierTitle()
    {
        return $this->_get(self::CARRIER_TITLE);
    }

    /**
     * Returns carrier code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->_get(self::CARRIER_CODE);
    }
}
