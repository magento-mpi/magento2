<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile info/options product view block
 */
class Magento_Payment_Block_Catalog_Product_View_Profile extends Magento_Core_Block_Template
{
    /**
     * Recurring profile instance
     *
     * @var Magento_Payment_Model_Recurring_Profile
     */
    protected $_profile = false;

    /**
     * Getter for schedule info
     * array(
     *     <title> => array('blah-blah', 'bla-bla-blah', ...)
     *     <title2> => ...
     * )
     * @return array
     */
    public function getScheduleInfo()
    {
        $scheduleInfo = array();
        foreach ($this->_profile->exportScheduleInfo() as $info) {
            $scheduleInfo[$info->getTitle()] = $info->getSchedule();
        }
        return $scheduleInfo;
    }

    /**
     * Render date input element
     *
     * @return string
     */
    public function getDateHtml()
    {
        if ($this->_profile->getStartDateIsEditable()) {
            $this->setDateHtmlId('recurring_start_date');
            $calendar = $this->getLayout()
                ->createBlock('Magento_Core_Block_Html_Date')
                ->setId('recurring_start_date')
                ->setName(Magento_Payment_Model_Recurring_Profile::BUY_REQUEST_START_DATETIME)
                ->setClass('datetime-picker input-text')
                ->setImage($this->getViewFileUrl('Magento_Core::calendar.gif'))
                ->setDateFormat(Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT))
                ->setTimeFormat(Mage::app()->getLocale()->getTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT));
            return $calendar->getHtml();
        }
    }

    /**
     * Determine current product and initialize its recurring profile model
     *
     * @return Magento_Payment_Block_Catalog_Product_View_Profile
     */
    protected function _prepareLayout()
    {
        $product = Mage::registry('current_product');
        if ($product) {
            $this->_profile = Mage::getModel('Magento_Payment_Model_Recurring_Profile')->importProduct($product);
        }
        return parent::_prepareLayout();
    }

    /**
     * If there is no profile information, the template will be unset, blocking the output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_profile) {
            $this->_template = null;
        }
        return parent::_toHtml();
    }
}
