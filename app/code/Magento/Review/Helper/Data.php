<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Helper;

/**
 * Default review helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    /**
     * Filter manager
     *
     * @var \Magento\Filter\FilterManager
     */
    protected $filter;

    /**
     * Core store config
     *
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * Escaper
     *
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Store\Model\Config $coreStoreConfig
     * @param \Magento\Escaper $escaper
     * @param \Magento\Filter\FilterManager $filter
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Store\Model\Config $coreStoreConfig,
        \Magento\Escaper $escaper,
        \Magento\Filter\FilterManager $filter
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_escaper = $escaper;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * Get review detail
     *
     * @param string $origDetail
     * @return string
     */
    public function getDetail($origDetail)
    {
        return nl2br($this->filter->truncate($origDetail, array('length' => 50)));
    }

    /**
     * Return short detail info in HTML
     *
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br($this->filter->truncate($this->_escaper->escapeHtml($origDetail), array('length' => 50)));
    }

    /**
     * Return an indicator of whether or not guest is allowed to write
     *
     * @return bool
     */
    public function getIsGuestAllowToWrite()
    {
        return $this->_coreStoreConfig->isSetFlag(self::XML_REVIEW_GUETS_ALLOW, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
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
        foreach ($this->getReviewStatuses() as $value => $label) {
            $result[] = array('value' => $value, 'label' => $label);
        }

        return $result;
    }
}
