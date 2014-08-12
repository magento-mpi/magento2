<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget\Document;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Doc\Document\Scheme;

/**
 * Outline navigation
 *
 */
class Outline extends Template
{
    /**
     * @var array
     */
    protected $document;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @param Context $context
     * @param Scheme $schemeReader
     * @param array $data
     */
    public function __construct(
        Context $context,
        Scheme $schemeReader,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scheme = $this->_request->getParam('doc_scheme');
        $this->document = $schemeReader->get($this->scheme . '.xml');
    }

    /**
     * Render outline in HTML
     *
     * @param int $level Level number for list item class to start from
     * @return string
     */
    public function renderOutlineHtml($level = 0)
    {
        $items = isset($this->document['content']) ? $this->document['content'] : [];
        $html = '';
        foreach ($items as $name => $item) {
            if ($name === 'Overview') {
                continue;
            }
            $html .= $this->renderOutlineItemHtml($item, $level);
        }
        return $html;
    }

    /**
     * Render outline item to html
     *
     * @param array $item
     * @param int $level Nesting level number
     * @return string
     */
    protected function renderOutlineItemHtml($item, $level = 0)
    {
        $children = isset($item['content']) ? $item['content'] : [];

        $childrenCount = count($children);
        $hasChildren = $childrenCount > 0;

        // prepare list item html classes
        $classes = [];
        $classes[] = 'level' . $level;
        if (isset($item['sortOrder'])) {
            $classes[] = 'nav-' . $item['sortOrder'];
        }
        if ($hasChildren) {
            $classes[] = 'parent';
        }
        $linkClass = '';
        // prepare list item attributes
        $attributes = [];
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';

        $html = array();
        $html[] = $htmlLi;

        $url = $this->getUrl('*/' . $this->scheme, ['article' => $item['name']]);
        $html[] = '<a href="' . $url . '"' . $linkClass . '>';
        $html[] = '<span>' . $item['label'] . '</span>';
        $html[] = '</a>';

        // render children
        $htmlChildren = '';
        foreach ($children as $child) {
            $htmlChildren .= $this->renderOutlineItemHtml($child, $level + 1);
        }
        if (!empty($htmlChildren)) {
            $html[] = '<ul class="level' . $level . '">';
            $html[] = $htmlChildren;
            $html[] = '</ul>';
        }

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }
}
