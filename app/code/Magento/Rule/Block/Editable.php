<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rule\Block;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\View\Element\AbstractBlock;

class Editable extends AbstractBlock implements RendererInterface
{
    /**
     * @var \Magento\Translate\InlineInterface
     */
    protected $inlineTranslate;

    /**
     * @param \Magento\View\Element\Context $context
     * @param \Magento\Translate\InlineInterface $inlineTranslate
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\Translate\InlineInterface $inlineTranslate,
        array $data = array()
    ) {
        $this->inlineTranslate = $inlineTranslate;
        parent::__construct($context, $data);
    }

    /**
     * Render element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     *
     * @see RendererInterface::render()
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->addClass('element-value-changer');
        $valueName = $element->getValueName();

        if ($valueName === '') {
            $valueName = '...';
        }

        if ($element->getShowAsText()) {
            $html = ' <input type="hidden" class="hidden" id="' .
                $element->getHtmlId() .
                '" name="' .
                $element->getName() .
                '" value="' .
                $element->getValue() .
                '"/> ' .
                htmlspecialchars(
                    $valueName
                ) . '&nbsp;';
        } else {
            $html = ' <span class="rule-param"' .
                ($element->getParamId() ? ' id="' .
                $element->getParamId() .
                '"' : '') .
                '>' .
                '<a href="javascript:void(0)" class="label">';

            if ($this->inlineTranslate->isAllowed()) {
                $html .= $this->escapeHtml($valueName);
            } else {
                $html .= $this->escapeHtml(
                    $this->filterManager->truncate($valueName, array('length' => 33, 'etc' => '...'))
                );
            }

            $html .= '</a><span class="element"> ' . $element->getElementHtml();

            if ($element->getExplicitApply()) {
                $html .= ' <a href="javascript:void(0)" class="rule-param-apply"><img src="' . $this->getViewFileUrl(
                    'images/rule_component_apply.gif'
                ) . '" class="v-middle" alt="' . __(
                    'Apply'
                ) . '" title="' . __(
                    'Apply'
                ) . '" /></a> ';
            }

            $html .= '</span></span>&nbsp;';
        }

        return $html;
    }
}
