<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ImportExport data helper
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Helper_Data extends Magento_ImportExport_Helper_Data
{
    /**
     * Reward data
     *
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * Customer balance data
     *
     * @var Enterprise_CustomerBalance_Helper_Data
     */
    protected $_customerBalanceData = null;

    /**
     * @param Enterprise_CustomerBalance_Helper_Data $customerBalanceData
     * @param Enterprise_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config_Modules $config
     */
    public function __construct(
        Enterprise_CustomerBalance_Helper_Data $customerBalanceData,
        Enterprise_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config_Modules $config
    ) {
        $this->_customerBalanceData = $customerBalanceData;
        $this->_rewardData = $rewardData;
        parent::__construct($coreHttp, $context, $config);
    }

    /**
     * Get operation header text
     *
     * @param string $type   operation type
     * @param string $action
     * @return string
     */
    public function getOperationHeaderText($type, $action = 'new')
    {
        $title = '';
        switch ($type) {
            case 'import':
                if ($action == 'edit') {
                    $title = __('Scheduled Import');
                } else {
                    $title = __('New Scheduled Import');
                }
                break;
            case 'export':
                if ($action == 'edit') {
                    $title = __('Scheduled Export');
                } else {
                    $title = __('New Scheduled Export');
                }
                break;
        }

        return $title;
    }

    /**
     * Get success operation save message
     *
     * @param string $type   operation type
     * @return string
     */
    public function getSuccessSaveMessage($type)
    {
        $message = '';
        switch ($type) {
            case 'import':
                $message = __('We saved the scheduled import.');
                break;
            case 'export':
                $message = __('We saved the scheduled report.');
                break;
        }

        return $message;
    }

    /**
     * Get success operation delete message
     *
     * @param string $type   operation type
     * @return string
     */
    public function getSuccessDeleteMessage($type)
    {
        $message = '';
        switch ($type) {
            case 'import':
                $message = __('We deleted the scheduled import.');
                break;
            case 'export':
                $message = __('We deleted the scheduled export.');
                break;
        }

        return $message;
    }

    /**
     * Get confirmation message
     *
     * @param string $type   operation type
     * @return string
     */
    public function getConfirmationDeleteMessage($type)
    {
        $message = '';
        switch ($type) {
            case 'import':
                $message = __('Are you sure you want to delete this scheduled import?');
                break;
            case 'export':
                $message = __('Are you sure you want to delete this scheduled export?');
                break;
        }

        return $message;
    }

    /**
     * Get notice operation message
     *
     * @param string $type   operation type
     * @return string
     */
    public function getNoticeMessage($type)
    {
        $message = '';
        if ($type == 'import') {
            $message = $this->getMaxUploadSizeMessage();
        }
        return $message;
    }

    /**
     * Is reward points enabled
     *
     * @return bool
     */
    public function isRewardPointsEnabled()
    {
        if ($this->isModuleEnabled('Enterprise_Reward')) {
            /** @var $rewardPointsHelper Enterprise_Reward_Helper_Data */
            $rewardPointsHelper = $this->_rewardData;
            return $rewardPointsHelper->isEnabled();
        }
        return false;
    }

    /**
     * Is store credit enabled
     *
     * @return bool
     */
    public function isCustomerBalanceEnabled()
    {
        if ($this->isModuleEnabled('Enterprise_CustomerBalance')) {
            return $this->_customerBalanceData->isEnabled();
        }
        return false;
    }
}
