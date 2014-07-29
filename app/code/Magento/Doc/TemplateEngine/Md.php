<?php
/**
 * {license}
 */

namespace Magento\Doc\TemplateEngine;

use Magento\Framework\View\TemplateEngineInterface;
use \cebe\markdown\Markdown;

class Md implements TemplateEngineInterface
{
    /**
     * @param Markdown $parser
     */
    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param \Magento\Framework\View\Element\BlockInterface $block
     * @param string $template
     * @param array $dictionary
     * @return string
     */
    public function render(
        \Magento\Framework\View\Element\BlockInterface $block,
        $template,
        array $dictionary = []
    ) {
        if (is_readable($template)) {
            ob_start();
            include $template;
            $content = ob_get_clean();
        } else {
            $content = $template;
        }

        $output = $this->parser->parse($content);
        return $output;
    }
}
