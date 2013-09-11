<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customers wishlist grid item action renderer for few action controls in one cell
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Grid\Renderer;

class Multiaction
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * Renders column
     *
     * @param  \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $html = '';
        $actions = $this->getColumn()->getActions();
        if (!empty($actions) && is_array($actions)) {
            $links = array();
            foreach ($actions as $action) {
                if (is_array($action)) {
                    $link = $this->_toLinkHtml($action, $row);
                    if ($link) {
                        $links[] = $link;
                    }
                }
            }
            $html = implode('<br />', $links);
        }

        if ($html == '') {
            $html = '&nbsp;';
        }

        return $html;
    }

    /**
     * Render single action as link html
     *
     * @param  array $action
     * @param  \Magento\Object $row
     * @return string
     */
    protected function _toLinkHtml($action, \Magento\Object $row)
    {
        $product = $row->getProduct();

        if (isset($action['process']) && $action['process'] == 'configurable') {
            if ($product->canConfigure()) {
                $style = '';
                $onClick = sprintf('onclick="return %s.configureItem(%s)"', $action['control_object'], $row->getId());
            } else {
                $style = 'style="color: #CCC;"';
                $onClick = '';
            }

            return sprintf('<a href="%s" %s %s>%s</a>', $action['url'], $style, $onClick, $action['caption']);
        } else {
            return parent::_toLinkHtml($action, $row);
        }
    }
}
