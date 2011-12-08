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
 * DataFlow Import Model
 *
 * @method Mage_Dataflow_Model_Resource_Import _getResource()
 * @method Mage_Dataflow_Model_Resource_Import getResource()
 * @method int getSessionId()
 * @method Mage_Dataflow_Model_Import setSessionId(int $value)
 * @method int getSerialNumber()
 * @method Mage_Dataflow_Model_Import setSerialNumber(int $value)
 * @method string getValue()
 * @method Mage_Dataflow_Model_Import setValue(string $value)
 * @method int getStatus()
 * @method Mage_Dataflow_Model_Import setStatus(int $value)
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Import extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('Mage_Dataflow_Model_Resource_Import');
    }

}
