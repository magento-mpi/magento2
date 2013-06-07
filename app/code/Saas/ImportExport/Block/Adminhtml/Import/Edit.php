<?php
/**
 * Import edit block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Import_Edit extends Mage_ImportExport_Block_Adminhtml_Import_Edit
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Saas_ImportExport';
    }
}
