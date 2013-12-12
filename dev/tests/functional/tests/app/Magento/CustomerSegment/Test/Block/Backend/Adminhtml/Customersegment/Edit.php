<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element\Locator;

/**
 * Class CustomerSegmentForm
 * Form for creation of the customer segment
 *
 * @package Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment
 */
class Edit extends FormTabs
{
    /**
     * Custom tab classes for customer segment form
     *
     * @var array
     */
    protected $tabClasses = array(
        'magento_customersegment_segment_tabs_general_section' =>
            '\\Magento\\CustomerSegment\\Test\\Block\\Backend\\Adminhtml\\Customersegment\\Edit\\Tab\\General'
    );
}
