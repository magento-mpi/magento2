<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml account controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_Config_System_StorageController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return file storage singleton
     *
     * @return Mage_Core_Model_File_Storage
     */
    protected function _getSyncSingleton()
    {
        return Mage::getSingleton('Mage_Core_Model_File_Storage');
    }

    /**
     * Return synchronize process status flag
     *
     * @return Mage_Core_Model_File_Storage_Flag
     */
    protected function _getSyncFlag()
    {
        return $this->_getSyncSingleton()->getSyncFlag();
    }

    /**
     * Synchronize action between storages
     *
     * @return void
     */
    public function synchronizeAction()
    {
        session_write_close();

        if (!isset($_REQUEST['storage'])) {
            return;
        }

        $flag = $this->_getSyncFlag();
        if ($flag && $flag->getState() == Mage_Core_Model_File_Storage_Flag::STATE_RUNNING
            && $flag->getLastUpdate()
            && time() <= (strtotime($flag->getLastUpdate()) + Mage_Core_Model_File_Storage_Flag::FLAG_TTL)
        ) {
            return;
        }

        $flag->setState(Mage_Core_Model_File_Storage_Flag::STATE_RUNNING)
            ->setFlagData(array())
            ->save();

        $storage = array('type' => (int) $_REQUEST['storage']);
        if (isset($_REQUEST['connection']) && !empty($_REQUEST['connection'])) {
            $storage['connection'] = $_REQUEST['connection'];
        }

        try {
            $this->_getSyncSingleton()->synchronize($storage);
        } catch (Exception $e) {
            Mage::logException($e);
            $flag->passError($e);
        }

        $flag->setState(Mage_Core_Model_File_Storage_Flag::STATE_FINISHED)->save();
    }

    /**
     * Retrieve synchronize process state and it's parameters in json format
     *
     * @return void
     */
    public function statusAction()
    {
        $result = array();
        $flag = $this->_getSyncFlag();

        if ($flag) {
            $state = $flag->getState();

            switch ($state) {
                case Mage_Core_Model_File_Storage_Flag::STATE_INACTIVE:
                    $flagData = $flag->getFlagData();
                    if (is_array($flagData)) {
                        if (isset($flagData['destination']) && !empty($flagData['destination'])) {
                            $result['destination'] = $flagData['destination'];
                        }
                    }
                    $state = Mage_Core_Model_File_Storage_Flag::STATE_INACTIVE;
                    break;
                case Mage_Core_Model_File_Storage_Flag::STATE_RUNNING:
                    if (!$flag->getLastUpdate()
                        || time() <= (strtotime($flag->getLastUpdate()) + Mage_Core_Model_File_Storage_Flag::FLAG_TTL)
                    ) {
                        $flagData = $flag->getFlagData();
                        if (is_array($flagData)
                            && isset($flagData['source']) && !empty($flagData['source'])
                            && isset($flagData['destination']) && !empty($flagData['destination'])
                        ) {
                            $result['message'] = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Synchronizing %1 to %2', $flagData['source'], $flagData['destination']);
                        } else {
                            $result['message'] = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Synchronizing...');
                        }
                        break;
                    } else {
                        $flagData = $flag->getFlagData();
                        if (is_array($flagData)
                            && !(isset($flagData['timeout_reached']) && $flagData['timeout_reached'])
                        ) {
                            Mage::logException(new Mage_Exception(
                                Mage::helper('Mage_Adminhtml_Helper_Data')->__('The timeout limit for response from synchronize process was reached.')
                            ));

                            $state = Mage_Core_Model_File_Storage_Flag::STATE_FINISHED;
                            $flagData['has_errors']         = true;
                            $flagData['timeout_reached']    = true;
                            $flag->setState($state)
                                ->setFlagData($flagData)
                                ->save();
                        }
                    }
                case Mage_Core_Model_File_Storage_Flag::STATE_FINISHED:
                case Mage_Core_Model_File_Storage_Flag::STATE_NOTIFIED:
                    $flagData = $flag->getFlagData();
                    if (!isset($flagData['has_errors'])) {
                        $flagData['has_errors'] = false;
                    }
                    $result['has_errors'] = $flagData['has_errors'];
                    break;
                default:
                    $state = Mage_Core_Model_File_Storage_Flag::STATE_INACTIVE;
                    break;
            }
        } else {
            $state = Mage_Core_Model_File_Storage_Flag::STATE_INACTIVE;
        }
        $result['state'] = $state;
        $result = Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
        $this->_response->setBody($result);
    }
}
