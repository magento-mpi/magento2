<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Helper;

/**
 * ImportExport data helper
 */
class Data extends \Magento\ImportExport\Helper\Data
{
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\File\Size $fileSize
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\File\Size $fileSize,
        $dbCompatibleMode = true
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $appState,
            $priceCurrency,
            $fileSize,
            $dbCompatibleMode
        );
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
}
