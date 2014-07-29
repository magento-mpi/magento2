<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Doc\Document\Filter;
use Magento\Doc\Document\Scheme;
use Magento\Doc\Document\Content;

class Example extends Template
{
    /**
     * @var \Magento\Doc\Document\Filter
     */
    protected $filter;

    /**
     * @var array
     */
    protected $dictionary;

    /**
     * @var string
     */
    protected $exampleName;

    /**
     * @var array
     */
    protected $example;

    /**
     * @param Context $context
     * @param Scheme $scheme
     * @param Content $content
     * @param Filter $filter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Scheme $scheme,
        Content $content,
        Filter $filter,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->exampleName = $this->_request->getParam('item');
        $this->example = $content->get('example/' . $this->exampleName . '.xhtml');

        $this->filter = $filter;
        $this->filter->setVariables([
                'render' => $this
            ]);
        $this->dictionary = $scheme->get('dictionary.xml');
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();
        if (!$this->getData('asis')) {
            return $this->processPlaceholders($html);
        } else {
            return $html;
        }
    }

    /**
     * @return array
     */
    public function getExampleHtml()
    {
        return $this->example;
    }

    protected function processPlaceholders($content)
    {
        return $this->filter->preProcess($content, $this->dictionary);
    }
}
