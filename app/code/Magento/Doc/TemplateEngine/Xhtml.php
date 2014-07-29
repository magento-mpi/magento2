<?php
/**
 * {license}
 */

namespace Magento\Doc\TemplateEngine;

use Magento\Framework\View\TemplateEngineInterface;

class Xhtml implements TemplateEngineInterface
{
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
            $output = ob_get_clean();
        } else{
            $output = $template;
        }

        return $output;
    }
}
