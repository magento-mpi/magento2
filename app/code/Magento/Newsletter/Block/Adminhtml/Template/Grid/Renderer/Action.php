<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter templates grid block action item renderer
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Newsletter\Block\Adminhtml\Template\Grid\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * Renderer for "Action" column in Newsletter templates grid
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        if($row->isValidForSend()) {
            $actions[] = array(
                'url' => $this->getUrl('*/queue/edit', array('template_id' => $row->getId())),
                'caption' => __('Queue Newsletter...')
            );
        }

        $actions[] = array(
            'url'     => $this->getUrl('*/*/preview', array('id'=>$row->getId())),
            'popup'   => true,
            'caption' => __('Preview')
        );

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }
}
