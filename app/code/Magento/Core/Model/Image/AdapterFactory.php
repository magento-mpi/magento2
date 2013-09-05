<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Image_AdapterFactory
{
    const ADAPTER_GD2   = 'GD2';
    const ADAPTER_IM    = 'IMAGEMAGICK';

    const XML_PATH_IMAGE_ADAPTER = 'dev/image/adapter';

    /**
     * @var array
     */
    protected $_adapterClasses;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var Magento_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Magento_Core_Model_Config $configModel
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Model_Config_Storage_WriterInterface $configWriter,
        Magento_Core_Model_Config $configModel
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeConfig = $storeConfig;
        $this->_configWriter = $configWriter;
        $this->_config = $configModel;
        $this->_adapterClasses = array(
            self::ADAPTER_GD2 => '\Magento\Image\Adapter\Gd2',
            self::ADAPTER_IM => '\Magento\Image\Adapter\ImageMagick',
        );
    }

    /**
     * Return specified image adapter
     *
     * @param string $adapterType
     * @return \Magento\Image\Adapter\AbstractAdapter
     * @throws InvalidArgumentException
     * @throws Exception if some of dependecies are missing
     */
    public function create($adapterType = null)
    {
        if (!isset($adapterType)) {
            $adapterType = $this->_getImageAdapterType();
        }
        if (!isset($this->_adapterClasses[$adapterType])) {
            throw new InvalidArgumentException(
                __('Invalid adapter selected.')
            );
        }
        $imageAdapter = $this->_objectManager->create($this->_adapterClasses[$adapterType]);
        $imageAdapter->checkDependencies();
        return $imageAdapter;
    }

    /**
     * Returns image adapter type
     *
     * @return string|null
     * @throws Magento_Core_Exception
     */
    public function _getImageAdapterType()
    {
        $adapterType = $this->_storeConfig->getConfig(self::XML_PATH_IMAGE_ADAPTER);
        if (!isset($adapterType)) {
            $errorMessage = '';
            foreach ($this->_adapterClasses as $adapter => $class) {
                try {
                    $this->_objectManager->create($class)->checkDependencies();

                    $this->_configWriter->save(
                        self::XML_PATH_IMAGE_ADAPTER,
                        $adapter,
                        Magento_Core_Model_Config::SCOPE_DEFAULT
                    );

                    $this->_config->reinit();
                    $adapterType = $adapter;
                    break;
                } catch (Exception $e) {
                    $errorMessage .= $e->getMessage();
                }
            }
            if (!isset($adapterType)) {
                 throw new Magento_Core_Exception($errorMessage);
            }
        }
        return $adapterType;
    }
}
