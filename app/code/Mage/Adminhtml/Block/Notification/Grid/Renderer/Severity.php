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
 * Adminhtml AdminNotification Severity Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Notification_Grid_Renderer_Severity
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $notice = Mage::getSingleton('Mage_AdminNotification_Model_Inbox');

        switch ($row->getData($this->getColumn()->getIndex())) {
            case Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL:
                $class = 'critical';
                $value = $notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL);
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR:
                $class = 'major';
                $value = $notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR);
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR:
                $class = 'minor';
                $value = $notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR);
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE:
                $class = 'notice';
                $value = $notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE);
                break;
        }
        return '<span class="grid-severity-' . $class . '"><span>' . $value . '</span></span>';
    }
}
