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
 * Theme files model class
 */
class Magento_Core_Model_Theme_File extends Magento_Core_Model_Abstract
    implements Magento_Core_Model_Theme_FileInterface
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventPrefix = 'theme_file';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventObject = 'file';

    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Magento_Core_Model_Theme_Customization_FileServiceFactory
     */
    protected $_fileServiceFactory;

    /**
     * @var Magento_Core_Model_Theme_Customization_FileInterface
     */
    protected $_fileService;

    /**
     * @var Magento_Core_Model_Theme_FlyweightFactory
     */
    protected $_themeFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Theme_FlyweightFactory $themeFactory
     * @param Magento_Core_Model_Theme_Customization_FileServiceFactory $fileServiceFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Theme_FlyweightFactory $themeFactory,
        Magento_Core_Model_Theme_Customization_FileServiceFactory $fileServiceFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_themeFactory = $themeFactory;
        $this->_fileServiceFactory = $fileServiceFactory;
    }

    /**
     * Theme files model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Resource_Theme_File');
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setCustomizationService(Magento_Core_Model_Theme_Customization_FileInterface $fileService)
    {
        $this->_fileService = $fileService;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException
     */
    public function getCustomizationService()
    {
        if (!$this->_fileService && $this->hasData('file_type')) {
            $this->_fileService = $this->_fileServiceFactory->create($this->getData('file_type'));
        } elseif (!$this->_fileService) {
            throw new UnexpectedValueException('Type of file is empty');
        }
        return $this->_fileService;
    }

    /**
     * {@inheritdoc}
     */
    public function setTheme(Magento_Core_Model_Theme $theme)
    {
        $this->_theme = $theme;
        $this->setData('theme_id', $theme->getId());
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Magento\MagentoException
     */
    public function getTheme()
    {
        $theme = $this->_themeFactory->create($this->getData('theme_id'));
        if (!$theme) {
            throw new \Magento\MagentoException('Theme id should be set');
        }
        return $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileName($fileName)
    {
        $this->setData('file_name', $fileName);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->getData('file_name') ?: basename($this->getData('file_path'));
    }

    /**
     * {@inheritdoc}
     */
    public function getFullPath()
    {
        return $this->getCustomizationService()->getFullPath($this);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getData('content');
    }

    /**
     * {@inheritdoc}
     */
    public function getFileInfo()
    {
        return array(
            'id'        => $this->getId(),
            'name'      => $this->getFileName(),
            'temporary' => $this->getData('is_temporary') ? $this->getId() : 0
        );
    }

    /**
     * Prepare file before it will be saved
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $fileService = $this->getCustomizationService();
        $fileService->prepareFile($this);
        $fileService->save($this);
        return parent::_beforeSave();
    }

    /**
     * Prepare file before it will be deleted
     *
     * @return $this
     */
    protected function _beforeDelete()
    {
        $fileService = $this->getCustomizationService();
        $fileService->delete($this);
        return parent::_beforeDelete();
    }
}
