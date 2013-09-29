<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled operation grid container
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled;

class Operation
    extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_addButtonLabel = __('Add Scheduled Export');
        parent::_construct();

        $this->_addButton('add_new_import', array(
            'label'   => __('Add Scheduled Import'),
            'onclick' => "setLocation('" . $this->getUrl('*/*/new', array('type' => 'import')) . "')",
            'class'   => 'add'
        ));

        $this->_blockGroup = 'Magento_ScheduledImportExport';
        $this->_controller = 'adminhtml_scheduled_operation';
        $this->_headerText = __('Scheduled Import/Export');
    }

    /**
     * Get create url
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new', array('type' => 'export'));
    }
}
