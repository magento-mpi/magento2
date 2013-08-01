<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter templates grid block action item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renderer for "Action" column in Newsletter templates grid
     *
     * @var Mage_Newsletter_Model_Template $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        if($row->isValidForSend()) {
            $actions[] = array(
                'url' => $this->getUrl('*/newsletter_queue/edit', array('template_id' => $row->getId())),
                'caption' => Mage::helper('Mage_Newsletter_Helper_Data')->__('Queue Newsletter...')
            );
        }

        $actions[] = array(
            'url'     => $this->getUrl('*/*/preview', array('id'=>$row->getId())),
            'popup'   => true,
            'caption' => Mage::helper('Mage_Newsletter_Helper_Data')->__('Preview')
        );

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }
}
