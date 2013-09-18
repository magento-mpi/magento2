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
namespace Magento\Payment\Block\Catalog\Product\View;

class Profile extends \Magento\Core\Block\Template
{
    /**
     * Recurring profile instance
     *
     * @var \Magento\Payment\Model\Recurring\Profile
     */
    protected $_profile = false;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

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
                ->createBlock('Magento\Core\Block\Html\Date')
                ->setId('recurring_start_date')
                ->setName(\Magento\Payment\Model\Recurring\Profile::BUY_REQUEST_START_DATETIME)
                ->setClass('datetime-picker input-text')
                ->setImage($this->getViewFileUrl('Magento_Core::calendar.gif'))
                ->setDateFormat(\Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT))
                ->setTimeFormat(\Mage::app()->getLocale()->getTimeFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT));
            return $calendar->getHtml();
        }
    }

    /**
     * Determine current product and initialize its recurring profile model
     *
     * @return \Magento\Payment\Block\Catalog\Product\View\Profile
     */
    protected function _prepareLayout()
    {
        $product = $this->_coreRegistry->registry('current_product');
        if ($product) {
            $this->_profile = \Mage::getModel('Magento\Payment\Model\Recurring\Profile')->importProduct($product);
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
