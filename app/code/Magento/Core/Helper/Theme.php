<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme data helper
 */
namespace Magento\Core\Helper;

class Theme extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Layout merge factory
     *
     * @var \Magento\Framework\View\Layout\ProcessorFactory
     */
    protected $_layoutProcessorFactory;

    /**
     * @var \Magento\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\Layout\ProcessorFactory $layoutProcessorFactory
     * @param \Magento\View\Asset\Repository $assetRepo
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Layout\ProcessorFactory $layoutProcessorFactory,
        \Magento\View\Asset\Repository $assetRepo
    ) {
        $this->_layoutProcessorFactory = $layoutProcessorFactory;
        $this->_assetRepo = $assetRepo;
        parent::__construct($context);
    }

    /**
     * Get CSS files of a given theme
     *
     * Returns an associative array of local assets with FileId used as keys:
     * array('Magento_Catalog::widgets.css' => \Magento\View\Asset\LocalInterface)
     * The array will be sorted by keys
     *
     * @param \Magento\Framework\View\Design\ThemeInterface $theme
     * @return \Magento\View\Asset\LocalInterface[]
     */
    public function getCssAssets($theme)
    {
        /** @var $layoutProcessor \Magento\Framework\View\Layout\ProcessorInterface */
        $layoutProcessor = $this->_layoutProcessorFactory->create(array('theme' => $theme));
        $layoutElement = $layoutProcessor->getFileLayoutUpdatesXml();

        /**
         * XPath selector to get CSS files from layout added for HEAD block directly
         */
        $xpathSelectorBlocks = '//block[@class="Magento\Theme\Block\Html\Head"]' .
            '/block[@class="Magento\Theme\Block\Html\Head\Css"]/arguments/argument[@name="file"]';

        /**
         * XPath selector to get CSS files from layout added for HEAD block using reference
         */
        $xpathSelectorRefs = '//referenceBlock[@name="head"]' .
            '/block[@class="Magento\Theme\Block\Html\Head\Css"]/arguments/argument[@name="file"]';

        $elements = array_merge(
            $layoutElement->xpath($xpathSelectorBlocks) ?: array(),
            $layoutElement->xpath($xpathSelectorRefs) ?: array()
        );

        $params = array(
            'area'       => $theme->getArea(),
            'themeModel' => $theme,
        );

        $result = array();
        foreach ($elements as $fileId) {
            $fileId = (string)$fileId;
            $result[$fileId] = $this->_assetRepo->createAsset($fileId, $params);
        }
        ksort($result);
        return $result;
    }
}
