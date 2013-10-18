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
namespace Magento\Review\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    /**
     * Core string
     *
     * @var \Magento\Core\Helper\String
     */
    protected $_coreString = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Escaper $escaper
     */
    public function __construct(
        \Magento\Core\Helper\String $coreString,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Escaper $escaper
    ) {
        $this->_coreString = $coreString;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_escaper = $escaper;
        parent::__construct($context);
    }

    public function getDetail($origDetail)
    {
        return nl2br($this->_coreString->truncate($origDetail, 50));
    }

    /**
     * getDetailHtml return short detail info in HTML
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br($this->_coreString->truncate($this->_escaper->escapeHtml($origDetail), 50));
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
            \Magento\Review\Model\Review::STATUS_APPROVED     => __('Approved'),
            \Magento\Review\Model\Review::STATUS_PENDING      => __('Pending'),
            \Magento\Review\Model\Review::STATUS_NOT_APPROVED => __('Not Approved'),
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
