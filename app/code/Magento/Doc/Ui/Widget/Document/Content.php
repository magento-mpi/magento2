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
use Magento\Doc\Document\Item;
use Magento\Doc\Document\Type\Factory;
use Magento\Doc\Document\Filter;

class Content extends Template
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
     * @var Item
     */
    protected $currentItem;

    /**
     * @var string
     */
    protected $itemTemplate;

    /**
     * @var string
     */
    protected $errorTemplate;

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
        $this->schemeName = $this->_request->getParam('doc_scheme');
        $this->document = $this->scheme->get($this->schemeName . '.xml');
        $this->dictionary = $this->scheme->get('help/dictionary.xml');
        $this->itemTemplate = $this->fetchView($this->getTemplateFile('Magento_Doc::html/widget/document/item.phtml'));
        $this->errorTemplate = $this->fetchView($this->getTemplateFile('Magento_Doc::html/widget/document/item.error.phtml'));
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

        return $this->processPlaceholders($content);
    }

    /**
     * Render current item's children HTML
     *
     * @return string
     */
    public function renderItemsHtml()
    {
        $html = '';
        if ($this->currentItem->getData('content')) {
            $items = $this->currentItem->getData('content');
            foreach ($items as $name => $item) {
                $html .= $this->renderItemHtml($name, $item);
            }
        }
        return $html;
    }

    /**
     * Return dictionary encoded to json format
     *
     * @return string
     */
    public function getDictionaryJson()
    {
        return json_encode($this->dictionary);
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
        $item['scheme'] = $this->schemeName;
        $this->currentItem = new Item($item);
        try {
            $content = $this->typeFactory->get($this->currentItem->getData('type'))->getContent($this->currentItem);
            $vars = [
                'render' => $this,
                'name' => $name,
                'content' => $content,
                'data' => $this->currentItem
            ];
            $this->filter->setVariables($vars);
            return $this->filter->preProcess($this->itemTemplate);
        } catch (\Exception $e) {
            $vars = [
                'render' => $this,
                'message' => $e->getMessage(),
                'data' => $this->currentItem
            ];
            $this->filter->setVariables($vars);
            return $this->filter->preProcess($this->errorTemplate);
        }
    }

    /**
     * Process template variables
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
