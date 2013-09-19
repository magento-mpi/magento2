<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Authorize.net DirectPost session model.
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Model\Directpost;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($eventManager, $coreHttp, $coreStoreConfig, $coreConfig, $data);
        $this->init('authorizenet_directpost', $sessionName);
    }

    /**
     * Add order IncrementId to session
     *
     * @param string $orderIncrementId
     */
    public function addCheckoutOrderIncrementId($orderIncrementId)
    {
        $orderIncIds = $this->getDirectPostOrderIncrementIds();
        if (!$orderIncIds) {
            $orderIncIds = array();
        }
        $orderIncIds[$orderIncrementId] = 1;
        $this->setDirectPostOrderIncrementIds($orderIncIds);
    }

    /**
     * Remove order IncrementId from session
     *
     * @param string $orderIncrementId
     */
    public function removeCheckoutOrderIncrementId($orderIncrementId)
    {
        $orderIncIds = $this->getDirectPostOrderIncrementIds();

        if (!is_array($orderIncIds)) {
            return;
        }

        if (isset($orderIncIds[$orderIncrementId])) {
            unset($orderIncIds[$orderIncrementId]);
        }
        $this->setDirectPostOrderIncrementIds($orderIncIds);
    }

    /**
     * Return if order incrementId is in session.
     *
     * @param string $orderIncrementId
     * @return bool
     */
    public function isCheckoutOrderIncrementIdExist($orderIncrementId)
    {
        $orderIncIds = $this->getDirectPostOrderIncrementIds();
        if (is_array($orderIncIds) && isset($orderIncIds[$orderIncrementId])) {
            return true;
        }
        return false;
    }
}
