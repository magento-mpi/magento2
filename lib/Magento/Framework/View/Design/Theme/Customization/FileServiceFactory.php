<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Design\Theme\Customization;

/**
 * Theme customization files factory
 */
class FileServiceFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param ConfigInterface $config
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, ConfigInterface $config)
    {
        $this->_objectManager = $objectManager;
        $this->_types = $config->getFileTypes();
    }

    /**
     * Create new instance
     *
     * @param string $type
     * @param array $data
     * @return \Magento\Framework\View\Design\Theme\Customization\FileInterface
     * @throws \InvalidArgumentException
     */
    public function create($type, array $data = array())
    {
        if (empty($this->_types[$type])) {
            throw new \InvalidArgumentException('Unsupported file type');
        }
        $fileService = $this->_objectManager->get($this->_types[$type], array($data));
        if (!$fileService instanceof \Magento\Framework\View\Design\Theme\Customization\FileInterface) {
            throw new \InvalidArgumentException('Service don\'t implement interface');
        }
        return $fileService;
    }
}
