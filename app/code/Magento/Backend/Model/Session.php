<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Auth session model
 */
namespace Magento\Backend\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        array $data = array()
    ) {
        parent::__construct($eventManager, $coreHttp, $data);
        $this->init('adminhtml');
    }
}
