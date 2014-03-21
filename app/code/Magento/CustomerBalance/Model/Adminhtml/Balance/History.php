<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model\Adminhtml\Balance;

/**
 * Customerbalance history model for adminhtml area
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\CustomerBalance\Model\Balance\History
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\View\DesignInterface $design,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_authSession = $authSession;
        parent::__construct(
            $context,
            $registry,
            $transportBuilder,
            $storeManager,
            $design,
            $coreStoreConfig,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Add information about admin user who changed customer balance
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $balance = $this->getBalanceModel();
        if ((int)$balance->getHistoryAction() == self::ACTION_UPDATED && !$balance->getUpdatedActionAdditionalInfo()) {
            $user = $this->_authSession->getUser();
            if ($user && $user->getUsername()) {
                if (!trim($balance->getComment())) {
                    $this->setAdditionalInfo(__('By admin: %1.', $user->getUsername()));
                } else {
                    $this->setAdditionalInfo(__('By admin: %1. (%2)', $user->getUsername(), $balance->getComment()));
                }
            }
        }

        return parent::_beforeSave();
    }
}
