<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Abstract block context object. Will be used as rule condition constructor modification point after release.
 * Important: Should not be modified by extension developers.
 */
namespace Magento\Rule\Model\Condition;

class Context implements \Magento\Framework\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Rule\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Rule\Model\ConditionFactory $conditionFactory
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Rule\Model\ConditionFactory $conditionFactory,
        \Magento\Framework\Logger $logger
    ) {
        $this->_assetRepo = $assetRepo;
        $this->_localeDate = $localeDate;
        $this->_layout = $layout;
        $this->_conditionFactory = $conditionFactory;
        $this->_logger = $logger;
    }

    /**
     * @return \Magento\Framework\View\Asset\Repository
     */
    public function getAssetRepository()
    {
        return $this->_assetRepo;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public function getLocaleDate()
    {
        return $this->_localeDate;
    }

    /**
     * @return \Magento\Framework\View\LayoutInterface
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
     * @return \Magento\Framework\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
