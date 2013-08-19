<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export edit block
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Block_Adminhtml_Export_Edit extends Mage_Backend_Block_Widget_Form_Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->removeButton('back')
            ->removeButton('reset')
            ->removeButton('save');

        $this->_objectId   = 'export_id';
        $this->_blockGroup = 'Mage_ImportExport';
        $this->_controller = 'adminhtml_export';
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Export');
    }
}
