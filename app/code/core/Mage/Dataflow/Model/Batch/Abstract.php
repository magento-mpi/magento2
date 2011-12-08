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
 * Dataflow Batch abstract model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Dataflow_Model_Batch_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Set batch data
     * automatic convert to serialize data
     *
     * @param mixed $data
     * @return Mage_Dataflow_Model_Batch_Abstract
     */
    public function setBatchData($data)
    {
        if ('"libiconv"' == ICONV_IMPL) {
            foreach ($data as &$value) {
                $value = iconv('utf-8', 'utf-8//IGNORE', $value);
            }
        }

        $this->setData('batch_data', serialize($data));

        return $this;
    }

    /**
     * Retrieve batch data
     * return unserialize data
     *
     * @return mixed
     */
    public function getBatchData()
    {
        $data = $this->_data['batch_data'];
        $data = unserialize($data);
        return $data;
    }

    /**
     * Retrieve id collection
     *
     * @param int $batchId
     * @return array
     */
    public function getIdCollection($batchId = null)
    {
        if (!is_null($batchId)) {
            $this->setBatchId($batchId);
        }
        return $this->getResource()->getIdCollection($this);
    }

    public function deleteCollection($batchId = null)
    {
        if (!is_null($batchId)) {
            $this->setBatchId($batchId);
        }
        return $this->getResource()->deleteCollection($this);
    }
}
