<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Static Block Widget
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Block\Widget;

class Block extends \Magento\Core\Block\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Storage for used widgets
     *
     * @var array
     */
    static protected $_widgetUsageMap = array();

    /**
     * Block factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
    }

    /**
     * Prepare block text and determine whether block output enabled or not
     * Prevent blocks recursion if needed
     *
     * @return \Magento\Cms\Block\Widget\Block
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $blockId = $this->getData('block_id');
        $blockHash = get_class($this) . $blockId;

        if (isset(self::$_widgetUsageMap[$blockHash])) {
            return $this;
        }
        self::$_widgetUsageMap[$blockHash] = true;

        if ($blockId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                $this->setText(
                    $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent())
                );
            }
        }

        unset(self::$_widgetUsageMap[$blockHash]);
        return $this;
    }
}
