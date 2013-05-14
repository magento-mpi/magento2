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
        parent::_construct();
    }
}
