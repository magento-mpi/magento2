<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Email templates grid block action item renderer
 *
 * @category   Magento
 * @package    Magento_Email
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Email\Block\Adminhtml\Template\Grid\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    public function render(\Magento\Object $row)
    {
        $actions = array();

        $actions[] = array(
            'url'		=>  $this->getUrl('adminhtml/*/preview', array('id'=>$row->getId())),
            'popup'     =>  true,
            'caption'	=>	__('Preview')
        );

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value), '\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        $html = array();
        $attributesObject = new \Magento\Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }
}
