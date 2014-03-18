<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Backend;

/**
 * System config file field backend model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class File extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Backend\Model\Config\Backend\File\RequestData\RequestDataInterface
     */
    protected $_requestData;

    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize = 0;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Core\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Backend\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Core\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Backend\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        \Magento\App\Filesystem $filesystem,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_uploaderFactory = $uploaderFactory;
        $this->_requestData = $requestData;
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::MEDIA_DIR);
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Save uploaded file before saving config value
     *
     * @return $this
     * @throws \Magento\Model\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $tmpName = $this->_requestData->getTmpName($this->getPath());
        $file = array();
        if ($tmpName) {
            $file['tmp_name'] = $tmpName;
            $file['name'] = $this->_requestData->getName($this->getPath());
        } elseif (!empty($value['tmp_name'])) {
            $file['tmp_name'] = $value['tmp_name'];
            $file['name'] = $value['value'];
        }
        if (!empty($file)) {
            $uploadDir = $this->_getUploadDir();
            try {
                $uploader = $this->_uploaderFactory->create(array('fileId' => $file));
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($uploadDir);
            } catch (\Exception $e) {
                throw new \Magento\Model\Exception($e->getMessage());
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $this->setValue('');
            } else {
                $this->unsValue();
            }
        }

        return $this;
    }

    /**
     * Validation callback for checking max file size
     *
     * @param  string $filePath Path to temporary uploaded file
     * @return void
     * @throws \Magento\Model\Exception
     */
    public function validateMaxSize($filePath)
    {
        $directory = $this->_filesystem->getDirectoryRead(\Magento\App\Filesystem::SYS_TMP_DIR);
        if (
            $this->_maxFileSize > 0 &&
            $directory->stat($directory->getRelativePath($filePath))['size'] > ($this->_maxFileSize * 1024)
        ) {
            throw new \Magento\Model\Exception(
                __('The file you\'re uploading exceeds the server size limit of %1 kilobytes.', $this->_maxFileSize)
            );
        }
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        $fieldConfig = $this->getFieldConfig();
        $dirParams = array_key_exists('upload_dir', $fieldConfig) ? $fieldConfig['upload_dir'] : array();
        return (is_array($dirParams) && array_key_exists('scope_info', $dirParams) && $dirParams['scope_info']);
    }

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throws \Magento\Model\Exception
     */
    protected function _getUploadDir()
    {
        $fieldConfig = $this->getFieldConfig();
        /* @var $fieldConfig \Magento\Simplexml\Element */

        if (!array_key_exists('upload_dir', $fieldConfig)) {
            throw new \Magento\Model\Exception(
                __('The base directory to upload file is not specified.')
            );
        }

        if (is_array($fieldConfig['upload_dir'])) {
            $uploadDir = $fieldConfig['upload_dir']['value'];
            if (array_key_exists('scope_info', $fieldConfig['upload_dir'])
                && $fieldConfig['upload_dir']['scope_info']
            ) {
                $uploadDir = $this->_appendScopeInfo($uploadDir);
            }

            if (array_key_exists('config', $fieldConfig['upload_dir'])) {
                $uploadDir = $this->_mediaDirectory->getAbsolutePath($uploadDir);
            }
        } else {
            $uploadDir = (string) $fieldConfig['upload_dir'];
        }

        return $uploadDir;
    }

    /**
     * Prepend path with scope info
     *
     * E.g. 'stores/2/path' , 'websites/3/path', 'default/path'
     *
     * @param string $path
     * @return string
     */
    protected function _prependScopeInfo($path)
    {
        $scopeInfo = $this->getScope();
        if ('default' != $this->getScope()) {
            $scopeInfo .= '/' . $this->getScopeId();
        }
        return $scopeInfo . '/' . $path;
    }

    /**
     * Add scope info to path
     *
     * E.g. 'path/stores/2' , 'path/websites/3', 'path/default'
     *
     * @param string $path
     * @return string
     */
    protected function _appendScopeInfo($path)
    {
        $path .= '/' . $this->getScope();
        if ('default' != $this->getScope()) {
            $path .= '/' . $this->getScopeId();
        }
        return $path;
    }

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array();
    }
}
