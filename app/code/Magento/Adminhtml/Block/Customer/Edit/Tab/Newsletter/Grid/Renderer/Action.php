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
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Edit\Tab\Newsletter\Grid\Renderer;

class Action extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(\Magento\Object $row)
    {
        $actions = array();

        $actions[] = array(
            '@'	=>  array(
                'href'  => $this->getUrl('*/newsletter_template/preview',
                    array(
                        'id'        => $row->getTemplateId(),
                        'subscriber'=> \Mage::registry('subscriber')->getId()
                    )
                                ),
                'target'=>	'_blank'
            ),
            '#'	=> __('View')
        );

        return $this->_actionsToHtml($actions);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        $html = array();
        $attributesObject = new \Magento\Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
    }

}
