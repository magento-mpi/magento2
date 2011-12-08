<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect Model Resource Template
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Template extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor, setting table and index field
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('xmlconnect_notification_template', 'template_id');
    }
}
