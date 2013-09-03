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
 * @method Magento_Rating_Model_Resource_Rating_Entity _getResource()
 * @method Magento_Rating_Model_Resource_Rating_Entity getResource()
 * @method string getEntityCode()
 * @method Magento_Rating_Model_Rating_Entity setEntityCode(string $value)
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Rating_Model_Rating_Entity extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Rating_Model_Resource_Rating_Entity');
    }

    public function getIdByCode($entityCode)
    {
        return $this->_getResource()->getIdByCode($entityCode);
    }
}
