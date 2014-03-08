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
     * @var \Magento\View\Asset\Service
     */
    protected $_assetService;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

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
     * @param \Magento\View\Asset\Service $assetService
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Rule\Model\ConditionFactory $conditionFactory
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\View\Asset\Service $assetService,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\View\LayoutInterface $layout,
        \Magento\Rule\Model\ConditionFactory $conditionFactory,
        \Magento\Logger $logger
    ) {
        $this->_assetService = $assetService;
        $this->_locale = $locale;
        $this->_layout = $layout;
        $this->_conditionFactory = $conditionFactory;
        $this->_logger = $logger;
    }

    /**
     * @return \Magento\View\Asset\Service
     */
    public function getAssetService()
    {
        return $this->_assetService;
    }

    /**
     * @return \Magento\Core\Model\LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
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
