<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default review helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Review_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * @param $origDetail
     * @return string
     */
    public function getDetail($origDetail)
    {
        return nl2br(Mage::helper('Magento_Core_Helper_String')->truncate($origDetail, 50));
    }

    /**
     * getDetailHtml return short detail info in HTML
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br(Mage::helper('Magento_Core_Helper_String')->truncate($this->escapeHtml($origDetail), 50));
    }

    /**
     * @return bool
     */
    public function getIsGuestAllowToWrite()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_REVIEW_GUETS_ALLOW);
    }

    /**
     * Get review statuses with their codes
     *
     * @return array
     */
    public function getReviewStatuses()
    {
        return array(
            Magento_Review_Model_Review::STATUS_APPROVED     => __('Approved'),
            Magento_Review_Model_Review::STATUS_PENDING      => __('Pending'),
            Magento_Review_Model_Review::STATUS_NOT_APPROVED => __('Not Approved'),
        );
    }

    /**
     * Get review statuses as option array
     *
     * @return array
     */
    public function getReviewStatusesOptionArray()
    {
        $result = array();
        foreach ($this->getReviewStatuses() as $k => $v) {
            $result[] = array('value' => $k, 'label' => $v);
        }

        return $result;
    }
}
