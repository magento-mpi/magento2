<?php
/**
 * Block class for search index notifications
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Block_Backend_Notifications extends Mage_Index_Block_Adminhtml_Notifications
{
    /**
     * Get index management url
     *
     * @return string
     */
    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/process/list');
    }
}
