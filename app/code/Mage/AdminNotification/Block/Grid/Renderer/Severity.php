<?php
/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Grid_Renderer_Severity
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var Mage_AdminNotification_Model_Inbox
     */
    protected $_notice;

    /**
     * @param Mage_Backend_Block_Context $context
     * @param Mage_AdminNotification_Model_Inbox $notice
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Context $context,
        Mage_AdminNotification_Model_Inbox $notice,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_notice = $notice;
    }

    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $class = '';
        $value = '';

        switch ($row->getData($this->getColumn()->getIndex())) {
            case Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL:
                $class = 'critical';
                $value = $this->_notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL);
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR:
                $class = 'major';
                $value = $this->_notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR);
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR:
                $class = 'minor';
                $value = $this->_notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR);
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE:
                $class = 'notice';
                $value = $this->_notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE);
                break;
        }
        return '<span class="grid-severity-' . $class . '"><span>' . $value . '</span></span>';
    }
}
