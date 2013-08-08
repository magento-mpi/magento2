<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Review status resource model
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Resource_Review_Status extends Magento_Core_Model_Resource_Db_Abstract
{

    /**
     * Resource status model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('review_status', 'status_id');
    }
}