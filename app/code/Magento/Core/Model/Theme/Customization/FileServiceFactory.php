<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme customization files factory
 */
namespace Magento\Core\Model\Theme\Customization;

class FileServiceFactory
{
    /**
     * XML path to definitions of customization services
     */
    const XML_PATH_CUSTOM_FILES = 'theme/customization';

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @var array
     */
    protected $_types = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Config $config
     */
    public function __construct(\Magento\ObjectManager $objectManager, \Magento\Core\Model\Config $config)
    {
        $this->_objectManager = $objectManager;
        $this->_config = $config;

        $convertNode = $config->getValue(self::XML_PATH_CUSTOM_FILES, 'default');
        if ($convertNode) {
            foreach ($convertNode as $name => $value) {
                $this->_types[$name] = $value;
            }
        }
    }

    /**
     * Create new instance
     *
     * @param $type
     * @param array $data
     * @return \Magento\Core\Model\Theme\Customization\FileInterface
     * @throws \InvalidArgumentException
     */
    public function create($type, array $data = array())
    {
        if (empty($this->_types[$type])) {
            throw new \InvalidArgumentException('Unsupported file type');
        }
        $fileService = $this->_objectManager->get($this->_types[$type], array($data));
        if (!$fileService instanceof \Magento\Core\Model\Theme\Customization\FileInterface) {
            throw new \InvalidArgumentException('Service don\'t implement interface');
        }
        return $fileService;
    }
}
