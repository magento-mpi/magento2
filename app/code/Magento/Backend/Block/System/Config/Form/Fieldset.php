<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config form fieldset renderer
 */
namespace Magento\Backend\Block\System\Config\Form;

class Fieldset
    extends \Magento\Backend\Block\AbstractBlock
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = array()
    ) {
        $this->_authSession = $authSession;
        parent::__construct($context, $data);
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        $html = $this->_getHeaderHtml($element);

        foreach ($element->getElements() as $field) {
            if ($field instanceof \Magento\Data\Form\Element\Fieldset) {
                $html .= '<tr id="row_' . $field->getHtmlId() . '"><td colspan="4">' . $field->toHtml() . '</td></tr>';
            } else {
                $html .= $field->toHtml();
            }
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * Return header html for fieldset
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getHeaderHtml($element)
    {
        if ($element->getIsNested()) {
            $html = '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '">';
        } else {
            $html = '<div class="' . $this->_getFrontendClass($element) . ' ">';
        }

        $html .= '<div class="entry-edit-head collapseable">'
            . '<span id="' . $element->getHtmlId() . '-link" class="entry-edit-head-link"></span>';

        $html .= $this->_getHeaderTitleHtml($element);

        $html .= '</div>';
        $html .= '<input id="'.$element->getHtmlId() . '-state" name="config_state[' . $element->getId()
            . ']" type="hidden" value="' . (int)$this->_isCollapseState($element) . '" />';
        $html .= '<fieldset class="' . $this->_getFieldsetCss() . '" id="' . $element->getHtmlId() . '">';
        $html .= '<legend>' . $element->getLegend() . '</legend>';

        $html .= $this->_getHeaderCommentHtml($element);

        // field label column
        $html .= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
        if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
            $html .= '<colgroup class="use-default" />';
        }
        $html .= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';

        return $html;
    }

    /**
     * Get frontend class
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getFrontendClass($element)
    {
        $group = $element->getGroup();
        $cssClass = isset($group['fieldset_css']) ? $group['fieldset_css'] : '';
        return 'section-config' . (empty($cssClass) ? '' : ' ' . $cssClass);
    }

    /**
     * Return header title part of html for fieldset
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        return '<a id="' . $element->getHtmlId() . '-head" href="#' . $element->getHtmlId()
            . '-link" onclick="Fieldset.toggleCollapse(\'' . $element->getHtmlId() . '\', \''
            . $this->getUrl('*/*/state') . '\'); return false;">' . $element->getLegend() . '</a>';
    }

    /**
     * Return header comment part of html for fieldset
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        return $element->getComment() ? '<div class="comment">' . $element->getComment() . '</div>' : '';
    }

    /**
     * Return full css class name for form fieldset
     *
     * @return string
     */
    protected function _getFieldsetCss()
    {
        /** @var \Magento\Backend\Model\Config\Structure\Element\Group $group */
        $group = $this->getGroup();
        $configCss = $group->getFieldsetCss();
        return 'config collapseable' . ($configCss ? ' ' . $configCss: '');
    }

    /**
     * Return footer html for fieldset
     * Add extra tooltip comments to elements
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getFooterHtml($element)
    {
        $html = '</tbody></table>';
        foreach ($element->getElements() as $field) {
            if ($field->getTooltip()) {
                $html .= sprintf('<div id="row_%s_comment" class="system-tooltip-box" style="display:none;">%s</div>',
                    $field->getId(), $field->getTooltip()
                );
            }
        }
        $html .= '</fieldset>' . $this->_getExtraJs($element);

        if ($element->getIsNested()) {
            $html .= '</td></tr>';
        } else {
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * Return js code for fieldset:
     * - observe fieldset rows;
     * - apply collapse;
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getExtraJs($element)
    {
        $htmlId = $element->getHtmlId();
        $output = "Fieldset.applyCollapse('{$htmlId}');";
        return $this->helper('Magento\Core\Helper\Js')->getScript($output);
    }

    /**
     * Collapsed or expanded fieldset when page loaded?
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        if ($element->getExpanded()) {
            return true;
        }
        $extra = $this->_authSession->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }
        return false;
    }
}
