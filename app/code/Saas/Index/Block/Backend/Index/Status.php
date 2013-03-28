<?php
/**
 * Block class for search index status
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Block_Backend_Index_Status extends Saas_Index_Block_Backend_Index
{
    /**
     * Initialize "controller"
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_Index::index/status.phtml');
        $this->_addButton('cancel_index', array(
            'label'    => $this->__('Cancel'),
            'class'    => 'cancel',
            'onclick'  => 'return goIndex.cancelIndex()',
            'disabled' => $this->isTaskProcessing() ? 'disabled' : '',
        ), 0, 0, 'cancel');

        parent::_construct();
    }
}
