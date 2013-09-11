<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ratings entity model
 *
 * @method \Magento\Rating\Model\Resource\Rating\Entity _getResource()
 * @method \Magento\Rating\Model\Resource\Rating\Entity getResource()
 * @method string getEntityCode()
 * @method \Magento\Rating\Model\Rating\Entity setEntityCode(string $value)
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rating\Model\Rating;

class Entity extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('\Magento\Rating\Model\Resource\Rating\Entity');
    }

    public function getIdByCode($entityCode)
    {
        return $this->_getResource()->getIdByCode($entityCode);
    }
}
