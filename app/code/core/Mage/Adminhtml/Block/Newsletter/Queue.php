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
 * Adminhtml queue grid block.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Queue extends Mage_Adminhtml_Block_Template
{

    protected $_template = 'newsletter/queue/list.phtml';

    protected function _beforeToHtml()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Newsletter_Queue_Grid', 'newsletter.queue.grid')
        );
        return parent::_beforeToHtml();
    }

}
