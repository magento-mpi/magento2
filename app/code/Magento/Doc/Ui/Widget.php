<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Doc\Document\Scheme;
use Magento\Doc\Document\Filter;

class Widget extends Template
{
    /**
     * Template pre-processor
     *
     * @var \Magento\Doc\Document\Filter
     */
    protected $filter;

    /**
     * Dictionary items array
     *
     * @var array
     */
    protected $dictionary;

    /**
     * Constructor
     *
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
        $this->filter->setVariables(
            [
                'render' => $this
            ]
        );
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
        return $this->preProcess($content);
    }

    /**
     * Process template placeholders and apply dictionary
     *
     * @param $content
     * @return mixed|string
     */
    protected function preProcess($content)
    {
        return $this->filter->preProcess($content, $this->dictionary);
    }
}
