<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Magento_GiftCardAccount_Model_Resource_History _getResource()
 * @method Magento_GiftCardAccount_Model_Resource_History getResource()
 * @method int getGiftcardaccountId()
 * @method Magento_GiftCardAccount_Model_History setGiftcardaccountId(int $value)
 * @method string getUpdatedAt()
 * @method Magento_GiftCardAccount_Model_History setUpdatedAt(string $value)
 * @method int getAction()
 * @method Magento_GiftCardAccount_Model_History setAction(int $value)
 * @method float getBalanceAmount()
 * @method Magento_GiftCardAccount_Model_History setBalanceAmount(float $value)
 * @method float getBalanceDelta()
 * @method Magento_GiftCardAccount_Model_History setBalanceDelta(float $value)
 * @method string getAdditionalInfo()
 * @method Magento_GiftCardAccount_Model_History setAdditionalInfo(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftCardAccount_Model_History extends Magento_Core_Model_Abstract
{
    const ACTION_CREATED  = 0;
    const ACTION_USED     = 1;
    const ACTION_SENT     = 2;
    const ACTION_REDEEMED = 3;
    const ACTION_EXPIRED  = 4;
    const ACTION_UPDATED  = 5;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_adminSession;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Backend_Model_Auth_Session $adminSession
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Backend_Model_Auth_Session $adminSession,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_adminSession = $adminSession;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }


    protected function _construct()
    {
        $this->_init('Magento_GiftCardAccount_Model_Resource_History');
    }


    /**
     * Get admin user
     *
     * @return null|Magento_User_Model_User
     */
    protected function _getAdminUser()
    {
        if ($this->_storeManager->getStore()->isAdmin()) {
            return $this->_adminSession->getUser();
        }

        return null;
    }

    public function getActionNamesArray()
    {
        return array(
            self::ACTION_CREATED  => __('Created'),
            self::ACTION_UPDATED  => __('Updated'),
            self::ACTION_SENT     => __('Sent'),
            self::ACTION_USED     => __('Used'),
            self::ACTION_REDEEMED => __('Redeemed'),
            self::ACTION_EXPIRED  => __('Expired'),
        );
    }

    protected function _getCreatedAdditionalInfo()
    {
        if ($this->getGiftcardaccount()->getOrder()) {
            $orderId = $this->getGiftcardaccount()->getOrder()->getIncrementId();
            return __('Order #%1.', $orderId);
        } else if ($user = $this->_getAdminUser()) {
            $username = $user->getUsername();
            if ($username) {
                return __('By admin: %1.', $username);
            }
        }

        return '';
    }

    protected function _getUsedAdditionalInfo()
    {
        if ($this->getGiftcardaccount()->getOrder()) {
            $orderId = $this->getGiftcardaccount()->getOrder()->getIncrementId();
            return __('Order #%1.', $orderId);
        }

        return '';
    }

    protected function _getSentAdditionalInfo()
    {
        $recipient = $this->getGiftcardaccount()->getRecipientEmail();
        if ($name = $this->getGiftcardaccount()->getRecipientName()) {
            $recipient = "{$name} <{$recipient}>";
        }

        $sender = '';
        if ($user = $this->_getAdminUser()) {
            if ($user->getUsername()) {
                $sender = $user->getUsername();
            }
        }

        if ($sender) {
            return __('Recipient: %1. By admin: %2.', $recipient, $sender);
        } else {
            return __('Recipient: %1.', $recipient);
        }
    }

    protected function _getRedeemedAdditionalInfo()
    {
        if ($customerId = $this->getGiftcardaccount()->getCustomerId()) {
            return __('Customer #%1.', $customerId);
        }
        return '';
    }

    protected function _getUpdatedAdditionalInfo()
    {
        if ($user = $this->_getAdminUser()) {
            $username = $user->getUsername();
            if ($username) {
                return __('By admin: %1.', $username);
            }
        }
        return '';
    }

    protected function _getExpiredAdditionalInfo()
    {
        return '';
    }

    protected function _beforeSave()
    {
        if (!$this->hasGiftcardaccount()) {
            Mage::throwException(__('Please assign a gift card account.'));
        }

        $this->setAction($this->getGiftcardaccount()->getHistoryAction());
        $this->setGiftcardaccountId($this->getGiftcardaccount()->getId());
        $this->setBalanceAmount($this->getGiftcardaccount()->getBalance());
        $this->setBalanceDelta($this->getGiftcardaccount()->getBalanceDelta());

        switch ($this->getGiftcardaccount()->getHistoryAction()) {
            case self::ACTION_CREATED:
                $this->setAdditionalInfo($this->_getCreatedAdditionalInfo());

                $this->setBalanceDelta($this->getBalanceAmount());
            break;
            case self::ACTION_USED:
                $this->setAdditionalInfo($this->_getUsedAdditionalInfo());
            break;
            case self::ACTION_SENT:
                $this->setAdditionalInfo($this->_getSentAdditionalInfo());
            break;
            case self::ACTION_REDEEMED:
                $this->setAdditionalInfo($this->_getRedeemedAdditionalInfo());
            break;
            case self::ACTION_UPDATED:
                $this->setAdditionalInfo($this->_getUpdatedAdditionalInfo());
            break;
            case self::ACTION_EXPIRED:
                $this->setAdditionalInfo($this->_getExpiredAdditionalInfo());
            break;
            default:
                Mage::throwException(__('Unknown history action.'));
            break;
        }

        return parent::_beforeSave();
    }
}
