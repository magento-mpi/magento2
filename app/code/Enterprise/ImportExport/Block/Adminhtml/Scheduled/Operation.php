<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled operation grid container
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation extends Magento_Backend_Block_Widget_Grid_Container
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

        $this->_blockGroup = 'Enterprise_ImportExport';
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
