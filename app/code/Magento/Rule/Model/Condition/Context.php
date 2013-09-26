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
     * @var \Magento\Core\Model\View\Url
     */
    protected $_viewUrl;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Rule\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Rule\Model\ConditionFactory $conditionFactory
     * @param \Magento\Core\Model\Logger $logger
     */
    public function __construct(
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\Layout $layout,
        \Magento\Rule\Model\ConditionFactory $conditionFactory,
        \Magento\Core\Model\Logger $logger
    ) {
        $this->_viewUrl = $viewUrl;
        $this->_locale = $locale;
        $this->_layout = $layout;
        $this->_conditionFactory = $conditionFactory;
        $this->_logger = $logger;
    }

    /**
     * @return \Magento\Core\Model\View\Url
     */
    public function getViewUrl()
    {
        return $this->_viewUrl;
    }

    /**
     * @return \Magento\Core\Model\LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @return \Magento\Core\Model\Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return \Magento\Core\Model\Layout
     */
    public function getConditionFactory()
    {
        return $this->_conditionFactory;
    }

    /**
     * @return \Magento\Core\Model\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
