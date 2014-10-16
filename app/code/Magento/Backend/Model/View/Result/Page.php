<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\View\Result;

use Magento\Framework\Translate;
use Magento\Framework\View;
use Magento\Framework\App;

class Page extends View\Result\Page
{
    /**
     * @var \Magento\Framework\App\Action\Title
     */
    protected $title;

    /**
     * @param View\Element\Template\Context $context
     * @param View\LayoutFactory $layoutFactory
     * @param View\Layout\Reader\Pool $layoutReaderPool
     * @param Translate\InlineInterface $translateInline
     * @param View\Page\ConfigFactory $pageConfigFactory
     * @param View\Page\Config\RendererFactory $pageConfigRendererFactory
     * @param View\Page\Layout\Reader $pageLayoutReader
     * @param View\Layout\BuilderFactory $layoutBuilderFactory
     * @param string $template
     * @param App\Action\Title $title
     * @param array $data
     */
    public function __construct(
        View\Element\Template\Context $context,
        View\LayoutFactory $layoutFactory,
        View\Layout\Reader\Pool $layoutReaderPool,
        Translate\InlineInterface $translateInline,
        View\Layout\BuilderFactory $layoutBuilderFactory,
        View\Page\ConfigFactory $pageConfigFactory,
        View\Page\Config\RendererFactory $pageConfigRendererFactory,
        View\Page\Layout\Reader $pageLayoutReader,
        $template,
        App\Action\Title $title
    )
    {
        parent::__construct(
            $context,
            $layoutFactory,
            $layoutReaderPool,
            $translateInline,
            $layoutBuilderFactory,
            $pageConfigFactory,
            $pageConfigRendererFactory,
            $pageLayoutReader,
            $template
        );
        $this->title = $title;
    }

    /**
     * Define active menu item in menu block
     *
     * @param string $itemId current active menu item
     * @return $this
     */
    public function setActiveMenu($itemId)
    {
        /** @var $menuBlock \Magento\Backend\Block\Menu */
        $menuBlock = $this->layout->getBlock('menu');
        $menuBlock->setActive($itemId);
        $parents = $menuBlock->getMenuModel()->getParentItems($itemId);
        $parents = array_reverse($parents);
        foreach ($parents as $item) {
            /** @var $item \Magento\Backend\Model\Menu\Item */
            $this->title->add($item->getTitle(), true);
        }
        return $this;
    }

    /**
     * @param string $label
     * @param string $title
     * @param string|null $link
     * @return $this
     */
    public function addBreadcrumb($label, $title, $link = null)
    {
        $this->layout->getBlock('breadcrumbs')->addLink($label, $title, $link);
        return $this;
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    public function addContent(View\Element\AbstractBlock $block)
    {
        return $this->moveBlockToContainer($block, 'content');
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    public function addLeft(View\Element\AbstractBlock $block)
    {
        return $this->moveBlockToContainer($block, 'left');
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    public function addJs(View\Element\AbstractBlock $block)
    {
        return $this->moveBlockToContainer($block, 'js');
    }

    /**
     * Set specified block as an anonymous child to specified container
     *
     * The block will be moved to the container from previous parent after all other elements
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @param string $containerName
     * @return $this
     */
    protected function moveBlockToContainer(View\Element\AbstractBlock $block, $containerName)
    {
        $this->layout->setChild($containerName, $block->getNameInLayout(), '');
        return $this;
    }
}
