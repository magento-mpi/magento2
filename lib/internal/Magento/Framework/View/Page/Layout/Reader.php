<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Page\Layout;

/**
 * Class Page layout reader
 */
class Reader
{
    /**
     * Merge cache suffix
     */
    const MERGE_CACHE_SUFFIX = 'page_layout';

    /**
     * @var \Magento\Framework\View\Design\Theme\ResolverInterface
     */
    protected $themeResolver;

    /**
     * @var \Magento\Framework\View\Layout\ProcessorFactory
     */
    protected $processorFactory;

    /**
     * @var \Magento\Framework\View\File\CollectorInterface
     */
    protected $pageLayoutFileSource;

    /**
     * @var \Magento\Framework\View\Layout\ProcessorInterface
     */
    protected $pageLayoutMerge;

    /**
     * @var \Magento\Framework\View\Layout\Reader\Pool
     */
    protected $reader;

    /**
     * @param \Magento\Framework\View\Design\Theme\ResolverInterface $themeResolver
     * @param \Magento\Framework\View\Layout\ProcessorFactory $processorFactory
     * @param \Magento\Framework\View\File\CollectorInterface $pageLayoutFileSource
     * @param \Magento\Framework\View\Layout\Reader\Pool $reader
     */
    public function __construct(
        \Magento\Framework\View\Design\Theme\ResolverInterface $themeResolver,
        \Magento\Framework\View\Layout\ProcessorFactory $processorFactory,
        \Magento\Framework\View\File\CollectorInterface $pageLayoutFileSource,
        \Magento\Framework\View\Layout\Reader\Pool $reader
    ) {
        $this->themeResolver = $themeResolver;
        $this->processorFactory = $processorFactory;
        $this->pageLayoutFileSource = $pageLayoutFileSource;
        $this->reader = $reader;
    }

    /**
     * Retrieve the layout update instance
     *
     * @return \Magento\Framework\View\Layout\ProcessorInterface
     */
    protected function getPageLayoutMerge()
    {
        if (!$this->pageLayoutMerge) {
            $this->pageLayoutMerge = $this->processorFactory->create([
                'theme' => $this->themeResolver->get(),
                'fileSource' => $this->pageLayoutFileSource,
                'cacheSuffix' => self::MERGE_CACHE_SUFFIX
            ]);
        }
        return $this->pageLayoutMerge;
    }

    /**
     * @param \Magento\Framework\View\Layout\Reader\Context $readerContext
     * @param string $pageLayout
     */
    public function read(\Magento\Framework\View\Layout\Reader\Context $readerContext, $pageLayout)
    {
        $this->getPageLayoutMerge()->load($pageLayout);
        $xml = $this->getPageLayoutMerge()->asSimplexml();
        $this->reader->readStructure($readerContext, $xml);
    }
}
