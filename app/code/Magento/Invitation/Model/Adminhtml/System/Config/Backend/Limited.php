<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Model\Adminhtml\System\Config\Backend;

/**
 * Backend model for max_invitation_amount_per_send to set it's pervious value
 * in case admin user will enter invalid data (for example zero) bc this value can't be unlimited.
 */
class Limited extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Message\ManagerInterface $messageManager,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
        $this->messageManager = $messageManager;
    }

    /**
     * Validating entered value if it will be 0 (unlimited)
     * throw notice and change it to old one
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if ((int)$this->getValue() <= 0) {
            $parameter = __('Max Invitations Allowed to be Sent at One Time');

            //if even old value is not valid we will have to you '1'
            $value = (int) $this->getOldValue();
            if ($value < 1) {
                $value = 1;

            }
            $this->setValue($value);
            $this->messageManager->addNotice(
                __('Please correct the value for "%1" parameter, otherwise we\'ll use the saved value instead.',
                    $parameter)
            );
        }
        return $this;
    }
}
