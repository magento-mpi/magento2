<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme change resource model
 */
class Magento_DesignEditor_Model_Theme_Resource_Change extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('vde_theme_change', 'change_id');
    }

    /**
     * {@inheritdoc}
     *
     * @param Magento_Core_Model_Abstract $change
     * @return $this
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $change)
    {
        if (!$change->getChangeTime()) {
            $change->setChangeTime($this->formatDate(true));
        }
        return $this;
    }
}
