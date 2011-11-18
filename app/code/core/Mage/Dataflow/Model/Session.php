<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * DataFlow Session Model
 *
 * @method Mage_Dataflow_Model_Resource_Session _getResource()
 * @method Mage_Dataflow_Model_Resource_Session getResource()
 * @method int getUserId()
 * @method Mage_Dataflow_Model_Session setUserId(int $value)
 * @method string getCreatedDate()
 * @method Mage_Dataflow_Model_Session setCreatedDate(string $value)
 * @method string getFile()
 * @method Mage_Dataflow_Model_Session setFile(string $value)
 * @method string getType()
 * @method Mage_Dataflow_Model_Session setType(string $value)
 * @method string getDirection()
 * @method Mage_Dataflow_Model_Session setDirection(string $value)
 * @method string getComment()
 * @method Mage_Dataflow_Model_Session setComment(string $value)
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Session extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('Mage_Dataflow_Model_Resource_Session');
    }

}
