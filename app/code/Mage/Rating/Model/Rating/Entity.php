<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ratings entity model
 *
 * @method Mage_Rating_Model_Resource_Rating_Entity _getResource()
 * @method Mage_Rating_Model_Resource_Rating_Entity getResource()
 * @method string getEntityCode()
 * @method Mage_Rating_Model_Rating_Entity setEntityCode(string $value)
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rating_Model_Rating_Entity extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Rating_Model_Resource_Rating_Entity');
    }

    public function getIdByCode($entityCode)
    {
        return $this->_getResource()->getIdByCode($entityCode);
    }
}
