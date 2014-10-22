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
     * Constructor
     *
     * @param View\Element\Template\Context $context
     * @param View\Layout\Reader\Pool $layoutReaderPool
     * @param Translate\InlineInterface $translateInline
     * @param View\Page\Config\RendererFactory $pageConfigRendererFactory
     * @param View\Page\Layout\Reader $pageLayoutReader
     * @param View\Layout\BuilderFactory $layoutBuilderFactory
     * @param string $template
     * @param App\Action\Title $title
     */
    public function __construct(
        View\Element\Template\Context $context,
        View\Layout\Reader\Pool $layoutReaderPool,
        Translate\InlineInterface $translateInline,
        View\Layout\BuilderFactory $layoutBuilderFactory,
        View\Page\Config\RendererFactory $pageConfigRendererFactory,
        View\Page\Layout\Reader $pageLayoutReader,
        $template,
        App\Action\Title $title
    ) {
        parent::__construct(
            $context,
            $layoutReaderPool,
            $translateInline,
            $layoutBuilderFactory,
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
     * Add link to breadcrumb block
     *
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
     * Add content to content section
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    public function addContent(View\Element\AbstractBlock $block)
    {
        return $this->moveBlockToContainer($block, 'content');
    }

    /**
     * Add block to left container
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    public function addLeft(View\Element\AbstractBlock $block)
    {
        return $this->moveBlockToContainer($block, 'left');
    }

    /**
     * Add javascript to head
     *
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
