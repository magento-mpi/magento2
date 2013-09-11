<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer orders grid action column item renderer
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rma\Block\Adminhtml\Customer\Edit\Tab\Renderer;

class Action
    extends \Magento\Adminhtml\Block\Sales\Reorder\Renderer\Action
{
    /**
     * Render field HRML for column
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $actions = array();
        if ($row->getIsReturnable()) {
            $actions[] = array(
                    '@' =>  array('href' => $this->getUrl('*/rma/new', array('order_id'=>$row->getId()))),
                    '#' =>  __('Return')
            );
        }
        $link1 = parent::render($row);
        $link2 = $this->_actionsToHtml($actions);
        $separator = $link1 && $link2 ? '<span class="separator">|</span>':'';
        return  $link1 . $separator . $link2;
    }
}
