<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website attribute backend
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Customer\Attribute\Backend;

class Website
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($logger, $data);
    }

    /**
     * Before save
     *
     * @param \Magento\Object $object
     * @return \Magento\Customer\Model\Customer\Attribute\Backend\Website
     */
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }

        if (!$object->hasData('website_id')) {
            $object->setData('website_id', $this->_storeManager->getStore()->getWebsiteId());
        }

        return $this;
    }
}
