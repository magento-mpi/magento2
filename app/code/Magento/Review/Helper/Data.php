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
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\Core\Helper\Context $context
     */
    public function __construct(
        \Magento\Core\Helper\String $coreString,
        \Magento\Core\Helper\Context $context
    ) {
        $this->_coreString = $coreString;
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
        return nl2br($this->_coreString->truncate($this->escapeHtml($origDetail), 50));
    }

    public function getIsGuestAllowToWrite()
    {
        return \Mage::getStoreConfigFlag(self::XML_REVIEW_GUETS_ALLOW);
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
