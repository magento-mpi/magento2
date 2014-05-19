<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Block\Adminhtml\Template\Grid\Renderer;

/**
 * Email templates grid block action item renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * Render grid column
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $actions = array();

        $actions[] = array(
            'url' => $this->getUrl('adminhtml/*/preview', array('id' => $row->getId())),
            'popup' => true,
            'caption' => __('Preview')
        );

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }

    /**
     * Get escaped value
     *
     * @param string $value
     * @return string
     */
    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value), '\\\'');
    }

    /**
     * Convert actions to html
     *
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions)
    {
        $html = array();
        $attributesObject = new \Magento\Framework\Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }
}
