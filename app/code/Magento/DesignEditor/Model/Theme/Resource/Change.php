<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Theme\Resource;

/**
 * Theme change resource model
 */
class Change extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Stdlib\DateTime $dateTime
     * @return void
     */
    public function __construct(\Magento\App\Resource $resource, \Magento\Stdlib\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Resource initialization
     *
     * @return void
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
