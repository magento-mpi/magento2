<?php
/**
 * {license_notice}
 *
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
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Locale model
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Recurring profile factory
     *
     * @var Magento_Payment_Model_Recurring_ProfileFactory
     */
    protected $_profileFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Payment_Model_Recurring_ProfileFactory $profileFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Payment_Model_Recurring_ProfileFactory $profileFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_coreRegistry = $registry;
        $this->_locale = $locale;
        $this->_profileFactory = $profileFactory;
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
                ->createBlock('Magento_Core_Block_Html_Date')
                ->setId('recurring_start_date')
                ->setName(Magento_Payment_Model_Recurring_Profile::BUY_REQUEST_START_DATETIME)
                ->setClass('datetime-picker input-text')
                ->setImage($this->getViewFileUrl('Magento_Core::calendar.gif'))
                ->setDateFormat($this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT))
                ->setTimeFormat($this->_locale->getTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT));
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
        $product = $this->_coreRegistry->registry('current_product');
        if ($product) {
            $this->_profile = $this->_profileFactory->create()->importProduct($product);
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
