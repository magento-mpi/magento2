<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog session model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Session\Validator $validator
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Validator $validator,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($validator, $logger, $eventManager, $coreHttp, $coreStoreConfig, $coreConfig, $data);
        $this->init('catalog', $sessionName);
    }

    public function getDisplayMode()
    {
        return $this->_getData('display_mode');
    }
}
