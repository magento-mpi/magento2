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

class Adapter extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
        $this->_imageFactory = $imageFactory;
    }

    /**
     * Checks if chosen image adapter available
     *
     * @throws \Magento\Framework\Model\Exception If some of adapter dependencies was not loaded
     * @return \Magento\Backend\Model\Config\Backend\File
     */
    protected function _beforeSave()
    {
        try {
            $this->_imageFactory->create($this->getValue());
        } catch (\Exception $e) {
            $message = __('The specified image adapter cannot be used because of: ' . $e->getMessage());
            throw new \Magento\Framework\Model\Exception($message);
        }

        return $this;
    }
}
