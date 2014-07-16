<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Config\System\Storage;

class Status extends \Magento\Backend\Controller\Adminhtml\System\Config\System\Storage
{
    /**
     * Retrieve synchronize process state and it's parameters in json format
     *
     * @return void
     */
    public function execute()
    {
        $result = array();
        $flag = $this->_getSyncFlag();

        if ($flag) {
            $state = $flag->getState();

            switch ($state) {
                case \Magento\Core\Model\File\Storage\Flag::STATE_INACTIVE:
                    $flagData = $flag->getFlagData();
                    if (is_array($flagData)) {
                        if (isset($flagData['destination']) && !empty($flagData['destination'])) {
                            $result['destination'] = $flagData['destination'];
                        }
                    }
                    $state = \Magento\Core\Model\File\Storage\Flag::STATE_INACTIVE;
                    break;
                case \Magento\Core\Model\File\Storage\Flag::STATE_RUNNING:
                    if (!$flag->getLastUpdate() || time() <= strtotime(
                        $flag->getLastUpdate()
                    ) + \Magento\Core\Model\File\Storage\Flag::FLAG_TTL
                    ) {
                        $flagData = $flag->getFlagData();
                        if (is_array(
                            $flagData
                        ) && isset(
                            $flagData['source']
                        ) && !empty($flagData['source']) && isset(
                            $flagData['destination']
                        ) && !empty($flagData['destination'])
                        ) {
                            $result['message'] = __(
                                'Synchronizing %1 to %2',
                                $flagData['source'],
                                $flagData['destination']
                            );
                        } else {
                            $result['message'] = __('Synchronizing...');
                        }
                        break;
                    } else {
                        $flagData = $flag->getFlagData();
                        if (is_array(
                            $flagData
                        ) && !(isset(
                            $flagData['timeout_reached']
                        ) && $flagData['timeout_reached'])
                        ) {
                            $this->_objectManager->get(
                                'Magento\Framework\Logger'
                            )->logException(
                                new \Magento\Framework\Exception(
                                    __('The timeout limit for response from synchronize process was reached.')
                                )
                            );

                            $state = \Magento\Core\Model\File\Storage\Flag::STATE_FINISHED;
                            $flagData['has_errors'] = true;
                            $flagData['timeout_reached'] = true;
                            $flag->setState($state)->setFlagData($flagData)->save();
                        }
                    }
                    // fall-through intentional
                case \Magento\Core\Model\File\Storage\Flag::STATE_FINISHED:
                case \Magento\Core\Model\File\Storage\Flag::STATE_NOTIFIED:
                    $flagData = $flag->getFlagData();
                    if (!isset($flagData['has_errors'])) {
                        $flagData['has_errors'] = false;
                    }
                    $result['has_errors'] = $flagData['has_errors'];
                    break;
                default:
                    $state = \Magento\Core\Model\File\Storage\Flag::STATE_INACTIVE;
                    break;
            }
        } else {
            $state = \Magento\Core\Model\File\Storage\Flag::STATE_INACTIVE;
        }
        $result['state'] = $state;
        $result = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result);
        $this->_response->representJson($result);
    }
}
