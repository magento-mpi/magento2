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
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\App\Resource $resource
     */
    public function __construct(\Magento\Stdlib\DateTime $dateTime, \Magento\App\Resource $resource)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

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
            $change->setChangeTime($this->dateTime->formatDate(true));
        }
        return $this;
    }
}
