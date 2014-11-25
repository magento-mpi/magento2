<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model\Adminhtml\Balance;

/**
 * Customerbalance history model for adminhtml area
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\CustomerBalance\Model\Balance\History
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_authSession = $authSession;
        parent::__construct(
            $context,
            $registry,
            $transportBuilder,
            $storeManager,
            $design,
            $scopeConfig,
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
    public function beforeSave()
    {
        $balance = $this->getBalanceModel();
        if (in_array((int)$balance->getHistoryAction(), array(self::ACTION_CREATED, self::ACTION_UPDATED))
            && !$balance->getUpdatedActionAdditionalInfo()
        ) {
            $user = $this->_authSession->getUser();
            if ($user && $user->getUsername()) {
                if (!trim($balance->getComment())) {
                    $this->setAdditionalInfo(__('By admin: %1.', $user->getUsername()));
                } else {
                    $this->setAdditionalInfo(__('By admin: %1. (%2)', $user->getUsername(), $balance->getComment()));
                }
            }
        }

        return parent::beforeSave();
    }
}
