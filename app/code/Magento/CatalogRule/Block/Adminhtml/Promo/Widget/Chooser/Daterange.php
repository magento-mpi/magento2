<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Date range promo widget chooser
 * Currently works without localized format
 */
namespace Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser;

class Daterange extends \Magento\Backend\Block\AbstractBlock
{
    /**
     * HTML ID of the element that will obtain the joined chosen values
     *
     * @var string
     */
    protected $_targetElementId = '';

    /**
     * From/To values to be rendered
     *
     * @var array
     */
    protected $_rangeValues     = array('from' => '', 'to' => '');

    /**
     * Range string delimiter for from/to dates
     *
     * @var string
     */
    protected $_rangeDelimiter  = '...';

    /**
     * @var \Magento\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Math\Random $mathRandom
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Math\Random $mathRandom,
        array $data = array()
    ) {
        $this->_formFactory = $formFactory;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $data);
    }

    /**
     * Render the chooser HTML
     * Target element should be set.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (empty($this->_targetElementId)) {
            return '';
        }

        $idSuffix = $this->mathRandom->getUniqueHash();
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $dateFields = array(
            'from' => __('From'),
            'to'   => __('To'),
        );
        foreach ($dateFields as $key => $label) {
            $form->addField("{$key}_{$idSuffix}", 'date', array(
                'format'   => \Magento\Stdlib\DateTime::DATE_INTERNAL_FORMAT, // hardcoded because hardcoded values delimiter
                'label'    => $label,
                'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
                'onchange' => "dateTimeChoose_{$idSuffix}()", // won't work through Event.observe()
                'value'    => $this->_rangeValues[$key],
            ));
        }
        return $form->toHtml() . "<script type=\"text/javascript\">
            dateTimeChoose_{$idSuffix} = function() {
                $('{$this->_targetElementId}').value = "
                    . "$('from_{$idSuffix}').value + '{$this->_rangeDelimiter}' + $('to_{$idSuffix}').value;
            };
            </script>";
    }

    /**
     * Target element ID setter
     *
     * @param string $value
     * @return \Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Daterange
     */
    public function setTargetElementId($value)
    {
        $this->_targetElementId = trim($value);
        return $this;
    }

    /**
     * Range values setter
     *
     * @param string $from
     * @param string $to
     * @return \Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Daterange
     */
    public function setRangeValues($from, $to)
    {
        $this->_rangeValues = array('from' => $from, 'to' => $to);
        return $this;
    }

    /**
     * Range values setter, string implementation.
     * Automatically attempts to split the string by delimiter
     *
     * @param string $delimitedString
     * @return \Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Daterange
     */
    public function setRangeValue($delimitedString)
    {
        $split = explode($this->_rangeDelimiter, $delimitedString, 2);
        $from = $split[0]; $to = '';
        if (isset($split[1])) {
            $to = $split[1];
        }
        return $this->setRangeValues($from, $to);
    }

    /**
     * Range delimiter setter
     *
     * @param string $value
     * @return \Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Daterange
     */
    public function setRangeDelimiter($value)
    {
        $this->_rangeDelimiter = (string)$value;
        return $this;
    }
}
