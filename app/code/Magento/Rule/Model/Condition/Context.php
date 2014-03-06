<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract block context object. Will be used as rule condition constructor modification point after release.
 * Important: Should not be modified by extension developers.
 */
namespace Magento\Rule\Model\Condition;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\View\Url
     */
    protected $_viewUrl;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Rule\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Rule\Model\ConditionFactory $conditionFactory
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\View\Url $viewUrl,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\View\LayoutInterface $layout,
        \Magento\Rule\Model\ConditionFactory $conditionFactory,
        \Magento\Logger $logger
    ) {
        $this->_viewUrl = $viewUrl;
        $this->_localeDate = $localeDate;
        $this->_layout = $layout;
        $this->_conditionFactory = $conditionFactory;
        $this->_logger = $logger;
    }

    /**
     * @return \Magento\View\Url
     */
    public function getViewUrl()
    {
        return $this->_viewUrl;
    }

    /**
     * @return \Magento\Stdlib\DateTime\TimezoneInterface
     */
    public function getLocaleDate()
    {
        return $this->_localeDate;
    }

    /**
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return \Magento\Rule\Model\ConditionFactory
     */
    public function getConditionFactory()
    {
        return $this->_conditionFactory;
    }

    /**
     * @return \Magento\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
