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
use Magento\Doc\Document\Scheme;
use Magento\Doc\Document\Filter;

class Dashboard extends Template
{
    /**
     * @var Scheme
     */
    protected $scheme;

    /**
     * @var \Magento\Doc\Document\Filter
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
     * @param Context $context
     * @param Scheme $scheme
     * @param Filter $filter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Scheme $scheme,
        Filter $filter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->filter = $filter;
        $this->dictionary = $scheme->get('dictionary.xml');
    }

    /**
     * Render document content HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $content = parent::_toHtml();
        return $this->processPlaceholders($content);
    }

    protected function processPlaceholders($content)
    {
        return $this->filter->preProcess($content, $this->dictionary);
    }
}
