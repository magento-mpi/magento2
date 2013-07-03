<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme change resource model
 */
class Mage_DesignEditor_Model_Theme_Resource_Change extends Mage_Core_Model_Resource_Db_Abstract
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
     * @param Mage_Core_Model_Abstract $change
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $change)
    {
        if (!$change->getChangeTime()) {
            $change->setChangeTime($this->formatDate(true));
        }
        return $this;
    }
}
