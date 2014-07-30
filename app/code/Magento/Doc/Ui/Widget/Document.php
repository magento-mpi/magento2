<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget;

use Magento\Framework\Object;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Doc\Document\Scheme;
use \Magento\Doc\Document\Type\Factory;
use Magento\Doc\Document\Filter;

class Document extends Template
{
    /**
     * @var Scheme
     */
    protected $scheme;

    /**
     * @var Factory
     */
    protected $typeFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var array
     */
    protected $document;

    /**
     * @var array
     */
    protected $dictionary;

    /**
     * @var string
     */
    protected $schemeName;

    /**
     * @var Object
     */
    protected $currentItem;

    /**
     * @var string
     */
    protected $itemTemplate;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Scheme $scheme
     * @param Factory $typeFactory
     * @param Filter $filter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Scheme $scheme,
        Factory $typeFactory,
        Filter $filter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scheme = $scheme;
        $this->typeFactory = $typeFactory;
        $this->filter = $filter;
        $this->schemeName = $this->getData('scheme');
        $this->document = $this->scheme->get($this->schemeName . '.xml');
        $this->dictionary = $this->scheme->get('dictionary.xml');
        $this->itemTemplate = $this->fetchView($this->getTemplateFile('Magento_Doc::html/widget/document/item.phtml'));
    }

    /**
     * Render document (item) content HTML
     *
     * @return string
     */
    public function renderDocumentHtml()
    {
        $content = '';
        if ($article = $this->_request->getParam('article')) {
            $item = $this->findItem($article);
            if ($item) {
                $items = [$article => $item];
            } else {
                $items = [];
            }
        } else {
            $items = isset($this->document['content']['Overview'])
                ? ['Overview' => $this->document['content']['Overview']]
            : $this->document['content'];
        }

        foreach ($items as $name => $item) {
            $content .=  $this->renderItemHtml($name, $item);
        }

        if (!$this->getData('asis')) {
            return $this->processPlaceholders($content);
        } else {
            return $content;
        }
    }

    /**
     * Render current item's children HTML
     *
     * @return string
     */
    public function renderItemsHtml()
    {
        $html = '';
        if ($this->currentItem->getContent()) {
            $items = $this->currentItem->getContent();
            foreach ($items as $name => $item) {
                $html .= $this->renderItemHtml($name, $item);
            }
        }
        return $html;
    }

    /**
     * Render single item's HTML
     *
     * @param string $name
     * @param array $item
     * @return string
     */
    protected function renderItemHtml($name, array $item)
    {
        $this->currentItem = new Object($item);
        $dictionary = [
            'render' => $this,
            'name' => $name,
            'schemeName' => $this->schemeName,
            'content' => $this->getItemContent($name, $item),
            'data' => $this->currentItem
        ];
        if ($name == 'UseCases') {
            var_dump($item);
        }
        $this->filter->setVariables($dictionary);
        return $this->filter->preProcess($this->itemTemplate);
    }

    /**
     * Get item's content
     *
     * @param string $name
     * @param array $item
     * @return string
     */
    protected function getItemContent($name, array $item)
    {
        $item['scheme'] = $this->schemeName;
        $filePath = $item['module'] . '::' . $this->schemeName . '/' . $name . '.xhtml';
        return $this->typeFactory->get($item['type'])->getContent($filePath, $item);
    }

    /**
     * Process template variables and apply dictionary to content
     *
     * @param string $content
     * @return string
     */
    protected function processPlaceholders($content)
    {
        return $this->filter->preProcess($content, $this->dictionary);
    }

    protected function findItem($name, array $parent = null)
    {
        if ($parent === null) {
            $parent = $this->document;
        }
        if (isset($parent['content'])) {
            foreach ($parent['content'] as $childName => $child) {
                if ($childName === $name) {
                    return $child;
                } else {
                    $result = $this->findItem($name, $child);
                    if ($result) {
                        return $result;
                    }
                }
            }
        }
        return null;
    }
}
