<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Doc\Ui\Widget\Document;

use Magento\Doc\Document\Filter;
use Magento\Doc\Document\Item;
use Magento\Doc\Document\ItemFactory;
use Magento\Doc\Document\Outline as DocumentOutline;
use Magento\Doc\Document\Type\Factory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Content extends Template
{
    /**
     * @var DocumentOutline
     */
    protected $outline;

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
    protected $outlineName;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

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
     * @param DocumentOutline $outline
     * @param Factory $typeFactory
     * @param ItemFactory $itemFactory
     * @param Filter $filter
     * @param array $data
     */
    public function __construct(
        Context $context,
        DocumentOutline $outline,
        Factory $typeFactory,
        ItemFactory $itemFactory,
        Filter $filter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->outline = $outline;
        $this->typeFactory = $typeFactory;
        $this->itemFactory = $itemFactory;
        $this->filter = $filter;
        $this->outlineName = $this->_request->getParam('doc_name');
        $this->document = $this->outline->get($this->outlineName . '.xml');
        $this->dictionary = $this->outline->get('help/dictionary.xml');
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
        if ($itemName = $this->_request->getParam('item')) {
            $item = $this->findItem($itemName);
            if (!empty($item)) {
                $items = [$itemName => $item];
            } else {
                $items = [];
            }
        } else {
            $items = isset($this->document['content']['Overview'])
                ? ['Overview' => $this->document['content']['Overview']]
            : (isset($this->document['content']) ? $this->document['content'] : []);
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
        $item['outline'] = $this->outlineName;
        $this->currentItem = $this->itemFactory->create(['data' => $item]);
        try {
            $content = $this->typeFactory->get($this->currentItem->getData('type'))->getContent($this->currentItem);
            $vars = [
                'render' => $this,
                'name' => $name,
                'display_content' => $content,
                'src_content' => $content,
                'data' => $this->currentItem,
            ];
            $this->filter->setVariables($vars);
            return $this->filter->preProcess($this->itemTemplate);
        } catch (\Exception $e) {
            $vars = [
                'render' => $this,
                'message' => $e->getMessage(),
                'data' => $this->currentItem,
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

    /**
     * Retrieve Document Outline item
     *
     * @param string $name
     * @param array $parent
     * @return array
     */
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
        return [];
    }
}
