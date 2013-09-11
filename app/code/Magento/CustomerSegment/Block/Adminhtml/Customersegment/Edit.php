<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit form for customer segment configuration
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Block\Adminhtml\Customersegment;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Initialize form
     * Add standard buttons
     * Add "Refresh Segment Data" button
     * Add "Save and Continue" button
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Magento_CustomerSegment';

        parent::_construct();

        /** @var $segment \Magento\CustomerSegment\Model\Segment */
        $segment = \Mage::registry('current_customer_segment');
        if ($segment && $segment->getId()) {
            $this->_addButton('match_customers', array(
                'label'     => __('Refresh Segment Data'),
                'onclick'   => 'setLocation(\'' . $this->getMatchUrl() . '\')',
            ), -1);
        }

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => __('Save and Continue Edit'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 3);
    }

    /**
     * Get url for run segment customers matching
     *
     * @return string
     */
    public function getMatchUrl()
    {
        $segment = \Mage::registry('current_customer_segment');
        return $this->getUrl('*/*/match', array('id'=>$segment->getId()));
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $segment = \Mage::registry('current_customer_segment');
        if ($segment->getSegmentId()) {
            return __("Edit Segment '%1'", $this->escapeHtml($segment->getName()));
        }
        else {
            return __('New Segment');
        }
    }
}
