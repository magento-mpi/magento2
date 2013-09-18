<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     * @param string $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Http $coreHttp,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($eventManager, $coreHttp, $coreStoreConfig, $coreConfig, $data);
        $this->init('magento_pbridge', $sessionName);
    }
}
