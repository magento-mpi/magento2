<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Banner;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class BannerForm
 * Backend banner form
 */
class BannerForm extends FormTabs
{
    /**
     * Locator for customer segment
     *
     * @var string
     */
    protected $useSegment = '[name="use_customer_segment"]';

    /**
     * Locator for apply banner to the Selected Customer Segments
     *
     * @var string
     */
    protected $customerSegmentOptions = '[name="customer_segment_ids[]"] option';

    /**
     * Check
     *
     * @param string $customerSegment [optional]
     * @param string $useSegment
     * @return bool
     */
    public function isCustomerSegmentVisible($customerSegment, $useSegment = 'Specified')
    {
        $this->_rootElement->find($this->useSegment, Locator::SELECTOR_CSS, 'select')->setValue($useSegment);
        $segments = $this->_rootElement->find($this->customerSegmentOptions, Locator::SELECTOR_CSS)
            ->getElements();
        foreach ($segments as $segment) {
            if ($customerSegment == $segment->getText()) {
                return true;
            }
        }
        return false;
    }
}
