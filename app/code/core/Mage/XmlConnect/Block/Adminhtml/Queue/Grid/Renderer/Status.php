<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml airmail queue grid block action item renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Queue_Grid_Renderer_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render grid row
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $str = '';
        if (is_numeric($row->getStatus())) {
            switch ($row->getStatus()) {
                case Mage_XmlConnect_Model_Queue::STATUS_IN_QUEUE:
                    $str = $this->__('In Queue');
                    break;
                case Mage_XmlConnect_Model_Queue::STATUS_CANCELED:
                    $str = $this->__('Cancelled');
                    break;
                case Mage_XmlConnect_Model_Queue::STATUS_COMPLETED:
                    $str = $this->__('Completed');
                    break;
                case Mage_XmlConnect_Model_Queue::STATUS_DELETED:
                    $str = $this->__('Deleted');
                    break;
            }
        }

        if ($str === '') {
            $str = $this->__('Undefined');
        }

        return $this->escapeHtml($str);
     }
}
