<?php
/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Block\Grid\Renderer;

class Severity extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\AdminNotification\Model\Inbox
     */
    protected $_notice;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\AdminNotification\Model\Inbox $notice
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\AdminNotification\Model\Inbox $notice,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_notice = $notice;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $class = '';
        $value = '';

        switch ($row->getData($this->getColumn()->getIndex())) {
            case \Magento\AdminNotification\Model\Inbox::SEVERITY_CRITICAL:
                $class = 'critical';
                $value = $this->_notice->getSeverities(\Magento\AdminNotification\Model\Inbox::SEVERITY_CRITICAL);
                break;
            case \Magento\AdminNotification\Model\Inbox::SEVERITY_MAJOR:
                $class = 'major';
                $value = $this->_notice->getSeverities(\Magento\AdminNotification\Model\Inbox::SEVERITY_MAJOR);
                break;
            case \Magento\AdminNotification\Model\Inbox::SEVERITY_MINOR:
                $class = 'minor';
                $value = $this->_notice->getSeverities(\Magento\AdminNotification\Model\Inbox::SEVERITY_MINOR);
                break;
            case \Magento\AdminNotification\Model\Inbox::SEVERITY_NOTICE:
                $class = 'notice';
                $value = $this->_notice->getSeverities(\Magento\AdminNotification\Model\Inbox::SEVERITY_NOTICE);
                break;
        }
        return '<span class="grid-severity-' . $class . '"><span>' . $value . '</span></span>';
    }
}
