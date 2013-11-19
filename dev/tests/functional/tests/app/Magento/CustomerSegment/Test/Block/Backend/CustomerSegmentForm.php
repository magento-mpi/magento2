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

namespace Magento\CustomerSegment\Test\Block\Backend;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element\Locator;

/**
 * Class CustomerSegmentForm
 * Form for creation of the customer segment
 *
 * @package Magento\CustomerSegment\Test\Block\Backend
 */
class CustomerSegmentForm extends FormTabs {
    /**
     * Custom tab classes for customer segment form
     *
     * @var array
     */
    protected $_tabClasses = array(
        'magento_customersegment_segment_tabs_general_section' =>
            '\\Magento\\Backend\\Test\\Block\\CustomerSegment\\Edit\\Tab\\Segment'
    );

    /**
     * Click save and continue button on form
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find('#save_and_continue_edit')->click();
    }
}