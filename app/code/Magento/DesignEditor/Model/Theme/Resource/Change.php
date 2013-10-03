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
namespace Magento\DesignEditor\Model\Theme\Resource;

class Change extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * @param \Magento\Core\Model\AbstractModel $change
     * @return $this
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $change)
    {
        if (!$change->getChangeTime()) {
            $change->setChangeTime($this->formatDate(true));
        }
        return $this;
    }
}
