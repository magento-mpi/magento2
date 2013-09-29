<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config image field backend model for Zend PDF generator
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Image;

class Adapter extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Core\Model\Image\AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Image\AdapterFactory $imageFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Image\AdapterFactory $imageFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $config,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_imageFactory = $imageFactory;
    }

    /**
     * Checks if chosen image adapter available
     *
     * @throws \Magento\Core\Exception if some of adapter dipendencies was not loaded
     * @return \Magento\Backend\Model\Config\Backend\File
     */
    protected function _beforeSave()
    {
        try {
            $this->_imageFactory->create($this->getValue());
        } catch (\Exception $e) {
            $message = __('The specified image adapter cannot be used because of: ' . $e->getMessage());
            throw new \Magento\Core\Exception($message);
        }

        return $this;
    }
}
