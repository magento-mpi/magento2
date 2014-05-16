<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Model\Rating;

/**
 * Ratings entity model
 *
 * @method \Magento\Review\Model\Resource\Rating\Entity _getResource()
 * @method \Magento\Review\Model\Resource\Rating\Entity getResource()
 * @method string getEntityCode()
 * @method \Magento\Review\Model\Rating\Entity setEntityCode(string $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Entity extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Review\Model\Resource\Rating\Entity');
    }

    /**
     * @param string $entityCode
     * @return int
     */
    public function getIdByCode($entityCode)
    {
        return $this->_getResource()->getIdByCode($entityCode);
    }
}
