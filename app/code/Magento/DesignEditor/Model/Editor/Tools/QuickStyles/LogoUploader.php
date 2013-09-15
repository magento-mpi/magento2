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
 * System config Logo image field backend model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
namespace Magento\DesignEditor\Model\Editor\Tools\QuickStyles;

class LogoUploader
    extends \Magento\Backend\Model\Config\Backend\Image\Logo
{
    /**
     * @param \Magento\Core\Model\Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\DesignEditor\Model\Config\Backend\File\RequestData $requestData
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        Magento_Core_Model_Registry $registry,
        \Magento\DesignEditor\Model\Config\Backend\File\RequestData $requestData,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $requestData, $filesystem, $resource, $resourceCollection, $data
        );
    }
}
