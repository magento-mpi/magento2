<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Doc\Ui;

use Magento\Doc\Document\Filter;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Widget extends Template
{
    /**
     * Template pre-processor
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Filter $filter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->filter = $filter;
        $this->filter->setVariables(
            [
                'render' => $this,
            ]
        );
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
     * @param string $content
     * @return mixed|string
     */
    protected function preProcess($content)
    {
        return $this->filter->preProcess($content);
    }
}
