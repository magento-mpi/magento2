<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg Images model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Cms_Model_Wysiwyg_Images_Storage extends Magento_Object
{
    const DIRECTORY_NAME_REGEXP = '/^[a-z0-9\-\_]+$/si';
    const THUMBS_DIRECTORY_NAME = '.thumbs';
    const THUMB_PLACEHOLDER_PATH_SUFFIX = 'Magento_Cms::images/placeholder_thumbnail.jpg';

    /**
     * Config object
     *
     * @var Magento_Core_Model_Config_Element
     */
    protected $_config;

    /**
     * Config object as array
     *
     * @var array
     */
    protected $_configAsArray;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Model_Image_AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * Core file storage database
     *
     * @var Magento_Core_Helper_File_Storage_Database
     */
    protected $_coreFileStorageDb = null;

    /**
     * Cms wysiwyg images
     *
     * @var Magento_Cms_Helper_Wysiwyg_Images
     */
    protected $_cmsWysiwygImages = null;

    /**
     * @var array
     */
    protected $_resizeParameters;

    /**
     * @var array
     */
    protected $_extensions;

    /**
     * @var array
     */
    protected $_dirs;

    /**
     * @var Magento_Backend_Model_Url
     */
    protected $_backendUrl;

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_session;

    /**
     * Directory database factory
     *
     * @var Magento_Core_Model_File_Storage_Directory_DatabaseFactory
     */
    protected $_directoryDatabaseFactory;

    /**
     * Storage database factory
     *
     * @var Magento_Core_Model_File_Storage_DatabaseFactory
     */
    protected $_storageDatabaseFactory;

    /**
     * Storage file factory
     *
     * @var Magento_Core_Model_File_Storage_FileFactory
     */
    protected $_storageFileFactory;

    /**
     * Storage collection factory
     *
     * @var Magento_Cms_Model_Wysiwyg_Images_Storage_CollectionFactory
     */
    protected $_storageCollectionFactory;

    /**
     * Dir
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * Uploader factory
     *
     * @var Magento_Core_Model_File_UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * Construct
     *
     * @param Magento_Backend_Model_Session $session
     * @param Magento_Backend_Model_Url $backendUrl
     * @param Magento_Cms_Helper_Wysiwyg_Images $cmsWysiwygImages
     * @param Magento_Core_Helper_File_Storage_Database $coreFileStorageDb
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Image_AdapterFactory $imageFactory
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Cms_Model_Wysiwyg_Images_Storage_CollectionFactory $storageCollectionFactory
     * @param Magento_Core_Model_File_Storage_FileFactory $storageFileFactory
     * @param Magento_Core_Model_File_Storage_DatabaseFactory $storageDatabaseFactory
     * @param Magento_Core_Model_File_Storage_Directory_DatabaseFactory $directoryDatabaseFactory
     * @param Magento_Core_Model_File_UploaderFactory $uploaderFactory
     * @param array $resizeParameters
     * @param array $extensions
     * @param array $dirs
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Backend_Model_Session $session,
        Magento_Backend_Model_Url $backendUrl,
        Magento_Cms_Helper_Wysiwyg_Images $cmsWysiwygImages,
        Magento_Core_Helper_File_Storage_Database $coreFileStorageDb,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Image_AdapterFactory $imageFactory,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_Dir $dir,
        Magento_Cms_Model_Wysiwyg_Images_Storage_CollectionFactory $storageCollectionFactory,
        Magento_Core_Model_File_Storage_FileFactory $storageFileFactory,
        Magento_Core_Model_File_Storage_DatabaseFactory $storageDatabaseFactory,
        Magento_Core_Model_File_Storage_Directory_DatabaseFactory $directoryDatabaseFactory,
        Magento_Core_Model_File_UploaderFactory $uploaderFactory,
        array $resizeParameters = array(),
        array $extensions = array(),
        array $dirs = array(),
        array $data = array()
    ) {
        $this->_session = $session;
        $this->_backendUrl = $backendUrl;
        $this->_cmsWysiwygImages = $cmsWysiwygImages;
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->setWorkingDirectory($cmsWysiwygImages->getStorageRoot());
        $this->_imageFactory = $imageFactory;
        $this->_viewUrl = $viewUrl;
        $this->_dir = $dir;
        $this->_storageCollectionFactory = $storageCollectionFactory;
        $this->_storageFileFactory = $storageFileFactory;
        $this->_storageDatabaseFactory = $storageDatabaseFactory;
        $this->_directoryDatabaseFactory = $directoryDatabaseFactory;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_resizeParameters = $resizeParameters;
        $this->_extensions = $extensions;
        $this->_dirs = $dirs;
        parent::__construct($data);
    }

    /**
     * Return one-level child directories for specified path
     *
     * @param string $path Parent directory path
     * @return Magento_Data_Collection_Filesystem
     */
    public function getDirsCollection($path)
    {
        if ($this->_coreFileStorageDb->checkDbUsage()) {
            /** @var Magento_Core_Model_File_Storage_Directory_Database $subDirectories */
            $subDirectories = $this->_directoryDatabaseFactory->create();
            $subDirectories->getSubdirectories($path);
            foreach ($subDirectories as $directory) {
                $fullPath = rtrim($path, DS) . DS . $directory['name'];
                $this->_filesystem->ensureDirectoryExists($fullPath, 0777, $path);
            }
        }

        $conditions = array('reg_exp' => array(), 'plain' => array());

        if ($this->_dirs['exclude']) {
            foreach ($this->_dirs['exclude'] as $dir) {
                $conditions[$dir->getAttribute('regexp') ? 'reg_exp' : 'plain'][$dir] = true;
            }
        }

        // "include" section takes precedence and can revoke directory exclusion
        if ($this->_dirs['include']) {
            foreach ($this->_dirs['include'] as $dir) {
                unset($conditions['regexp'][(string) $dir], $conditions['plain'][$dir]);
            }
        }

        $regExp = $conditions['reg_exp'] ? ('~' . implode('|', array_keys($conditions['reg_exp'])) . '~i') : null;
        $collection = $this->getCollection($path)
            ->setCollectDirs(true)
            ->setCollectFiles(false)
            ->setCollectRecursively(false);
        $storageRootLength = strlen($this->_cmsWysiwygImages->getStorageRoot());

        foreach ($collection as $key => $value) {
            $rootChildParts = explode(DIRECTORY_SEPARATOR, substr($value->getFilename(), $storageRootLength));

            if (array_key_exists($rootChildParts[0], $conditions['plain'])
                || ($regExp && preg_match($regExp, $value->getFilename()))) {
                $collection->removeItemByKey($key);
            }
        }

        return $collection;
    }

    /**
     * Return files
     *
     * @param string $path Parent directory path
     * @param string $type Type of storage, e.g. image, media etc.
     * @return Magento_Data_Collection_Filesystem
     */
    public function getFilesCollection($path, $type = null)
    {
        if ($this->_coreFileStorageDb->checkDbUsage()) {
            $files = $this->_storageDatabaseFactory->create()->getDirectoryFiles($path);

            /** @var Magento_Core_Model_File_Storage_File $fileStorageModel */
            $fileStorageModel = $this->_storageFileFactory->create();
            foreach ($files as $file) {
                $fileStorageModel->saveFile($file);
            }
        }

        $collection = $this->getCollection($path)
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false)
            ->setOrder('mtime', Magento_Data_Collection::SORT_ORDER_ASC);

        // Add files extension filter
        if ($allowed = $this->getAllowedExtensions($type)) {
            $collection->setFilesFilter('/\.(' . implode('|', $allowed). ')$/i');
        }

        // prepare items
        foreach ($collection as $item) {
            $item->setId($this->_cmsWysiwygImages->idEncode($item->getBasename()));
            $item->setName($item->getBasename());
            $item->setShortName($this->_cmsWysiwygImages->getShortFilename($item->getBasename()));
            $item->setUrl($this->_cmsWysiwygImages->getCurrentUrl() . $item->getBasename());

            if ($this->isImage($item->getBasename())) {
                $thumbUrl = $this->getThumbnailUrl($item->getFilename(), true);
                // generate thumbnail "on the fly" if it does not exists
                if (!$thumbUrl) {
                    $thumbUrl = $this->_backendUrl->getUrl('*/*/thumbnail', array('file' => $item->getId()));
                }

                $size = @getimagesize($item->getFilename());

                if (is_array($size)) {
                    $item->setWidth($size[0]);
                    $item->setHeight($size[1]);
                }
            } else {
                $thumbUrl = $this->_viewUrl->getViewFileUrl(self::THUMB_PLACEHOLDER_PATH_SUFFIX);
            }

            $item->setThumbUrl($thumbUrl);
        }

        return $collection;
    }

    /**
     * Storage collection
     *
     * @param string $path Path to the directory
     * @return Magento_Cms_Model_Wysiwyg_Images_Storage_Collection
     */
    public function getCollection($path = null)
    {
        /** @var Magento_Cms_Model_Wysiwyg_Images_Storage_Collection $collection */
        $collection = $this->_storageCollectionFactory->create();
        if ($path !== null) {
            $collection->addTargetDir($path);
        }
        return $collection;
    }

    /**
     * Create new directory in storage
     *
     * @param string $name New directory name
     * @param string $path Parent directory path
     * @return array New directory info
     * @throws Magento_Core_Exception
     */
    public function createDirectory($name, $path)
    {
        if (!preg_match(self::DIRECTORY_NAME_REGEXP, $name)) {
            throw new Magento_Core_Exception(
                __('Please correct the folder name. Use only letters, numbers, underscores and dashes.'));
        }
        if (!$this->_filesystem->isDirectory($path) || !$this->_filesystem->isWritable($path)) {
            $path = $this->_cmsWysiwygImages->getStorageRoot();
        }

        $newPath = $path . DS . $name;

        if ($this->_filesystem->isDirectory($newPath, $path)) {
            throw new Magento_Core_Exception(
                __('We found a directory with the same name. Please try another folder name.'));
        }

        $this->_filesystem->createDirectory($newPath);
        try {
            if ($this->_coreFileStorageDb->checkDbUsage()) {
                $relativePath = $this->_coreFileStorageDb->getMediaRelativePath($newPath);
                $this->_directoryDatabaseFactory->create()->createRecursive($relativePath);
            }

            $result = array(
                'name'          => $name,
                'short_name'    => $this->_cmsWysiwygImages->getShortFilename($name),
                'path'          => $newPath,
                'id'            => $this->_cmsWysiwygImages->convertPathToId($newPath)
            );
            return $result;
        } Catch (Magento_Filesystem_Exception $e) {
            throw new Magento_Core_Exception(__('We cannot create a new directory.'));
        }
    }

    /**
     * Recursively delete directory from storage
     *
     * @param string $path Target dir
     * @return void
     * @throws Magento_Core_Exception
     */
    public function deleteDirectory($path)
    {
        // prevent accidental root directory deleting
        $rootCmp = rtrim($this->_cmsWysiwygImages->getStorageRoot(), DS);
        $pathCmp = rtrim($path, DS);

        if ($rootCmp == $pathCmp) {
            throw new Magento_Core_Exception(
                __('We cannot delete root directory %1.', $path)
            );
        }


        if ($this->_coreFileStorageDb->checkDbUsage()) {
            $this->_directoryDatabaseFactory->create()->deleteDirectory($path);
        }
        try {
            $this->_filesystem->delete($path);
        } catch (Magento_Filesystem_Exception $e) {
            throw new Magento_Core_Exception(__('We cannot delete directory %1.', $path));
        }

        if (strpos($pathCmp, $rootCmp) === 0) {
            $this->_filesystem->delete(
                $this->getThumbnailRoot() . DS . ltrim(substr($pathCmp, strlen($rootCmp)), '\\/')
            );
        }
    }

    /**
     * Delete file (and its thumbnail if exists) from storage
     *
     * @param string $target File path to be deleted
     * @return Magento_Cms_Model_Wysiwyg_Images_Storage
     */
    public function deleteFile($target)
    {
        if ($this->_filesystem->isFile($target)) {
            $this->_filesystem->delete($target);
        }
        $this->_coreFileStorageDb->deleteFile($target);

        $thumb = $this->getThumbnailPath($target, true);
        if ($thumb) {
            if ($this->_filesystem->isFile($thumb)) {
                $this->_filesystem->delete($thumb);
            }
            $this->_coreFileStorageDb->deleteFile($thumb);
        }
        return $this;
    }


    /**
     * Upload and resize new file
     *
     * @param string $targetPath Target directory
     * @param string $type Type of storage, e.g. image, media etc.
     * @return array File info Array
     * @throws Magento_Core_Exception
     */
    public function uploadFile($targetPath, $type = null)
    {
        /** @var Magento_Core_Model_File_Uploader $uploader */
        $uploader = $this->_uploaderFactory->create(array('fileId' => 'image'));
        $allowed = $this->getAllowedExtensions($type);
        if ($allowed) {
            $uploader->setAllowedExtensions($allowed);
        }
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $result = $uploader->save($targetPath);

        if (!$result) {
            throw new Magento_Core_Exception(__('We cannot upload the file.') );
        }

        // create thumbnail
        $this->resizeFile($targetPath . DS . $uploader->getUploadedFileName(), true);

        $result['cookie'] = array(
            'name'     => session_name(),
            'value'    => $this->getSession()->getSessionId(),
            'lifetime' => $this->getSession()->getCookieLifetime(),
            'path'     => $this->getSession()->getCookiePath(),
            'domain'   => $this->getSession()->getCookieDomain()
        );

        return $result;
    }

    /**
     * Thumbnail path getter
     *
     * @param  string $filePath original file path
     * @param  boolean $checkFile OPTIONAL is it necessary to check file availability
     * @return string | false
     */
    public function getThumbnailPath($filePath, $checkFile = false)
    {
        $mediaRootDir = $this->_cmsWysiwygImages->getStorageRoot();

        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbPath = $this->getThumbnailRoot() . DS . substr($filePath, strlen($mediaRootDir));

            if (!$checkFile || $this->_filesystem->isReadable($thumbPath)) {
                return $thumbPath;
            }
        }

        return false;
    }

    /**
     * Thumbnail URL getter
     *
     * @param  string $filePath original file path
     * @param  boolean $checkFile OPTIONAL is it necessary to check file availability
     * @return string | false
     */
    public function getThumbnailUrl($filePath, $checkFile = false)
    {
        $mediaRootDir = $this->_cmsWysiwygImages->getStorageRoot();

        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbSuffix = self::THUMBS_DIRECTORY_NAME . DS . substr($filePath, strlen($mediaRootDir));

            if (! $checkFile || $this->_filesystem->isReadable($mediaRootDir . $thumbSuffix)) {
                $randomIndex = '?rand=' . time();
                return str_replace('\\', '/', $this->_cmsWysiwygImages->getBaseUrl() . $thumbSuffix) . $randomIndex;
            }
        }

        return false;
    }

    /**
     * Create thumbnail for image and save it to thumbnails directory
     *
     * @param string $source Image path to be resized
     * @param bool $keepRation Keep aspect ratio or not
     * @return bool|string Resized filepath or false if errors were occurred
     */
    public function resizeFile($source, $keepRation = true)
    {
        if (!$this->_filesystem->isFile($source)
            || !$this->_filesystem->isReadable($source)
        ) {
            return false;
        }

        $targetDir = $this->getThumbsPath($source);
        if (!$this->_filesystem->isWritable($targetDir)) {
            $this->_filesystem->createDirectory($targetDir);
        }
        if (!$this->_filesystem->isWritable($targetDir)) {
            return false;
        }
        $image = $this->_imageFactory->create();
        $image->open($source);
        $image->keepAspectRatio($keepRation);
        $image->resize($this->_resizeParameters['width'], $this->_resizeParameters['height']);
        $dest = $targetDir . DS . pathinfo($source, PATHINFO_BASENAME);
        $image->save($dest);
        if ($this->_filesystem->isFile($dest)) {
            return $dest;
        }
        return false;
    }

    /**
     * Resize images on the fly in controller action
     *
     * @param string File basename
     * @return bool|string Thumbnail path or false for errors
     */
    public function resizeOnTheFly($filename)
    {
        $path = $this->getSession()->getCurrentPath();
        if (!$path) {
            $path = $this->_cmsWysiwygImages->getCurrentPath();
        }
        return $this->resizeFile($path . DS . $filename);
    }

    /**
     * Return thumbnails directory path for file/current directory
     *
     * @param bool|string $filePath Path to the file
     * @return string
     */
    public function getThumbsPath($filePath = false)
    {
        $mediaRootDir = $this->_dir->getDir(Magento_Core_Model_Dir::MEDIA);
        $thumbnailDir = $this->getThumbnailRoot();

        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= DS . dirname(substr($filePath, strlen($mediaRootDir)));
        }

        return $thumbnailDir;
    }

    /**
     * Storage session
     *
     * @return Magento_Adminhtml_Model_Session
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Prepare allowed_extensions config settings
     *
     * @param string $type Type of storage, e.g. image, media etc.
     * @return array Array of allowed file extensions
     */
    public function getAllowedExtensions($type = null)
    {
        if (is_string($type) && array_key_exists("{$type}_allowed", $this->_extensions)) {
            $allowed = $this->_extensions["{$type}_allowed"];
        } else {
            $allowed = $this->_extensions['allowed'];
        }

        return array_keys(array_filter($allowed));
    }

    /**
     * Thumbnail root directory getter
     *
     * @return string
     */
    public function getThumbnailRoot()
    {
        return $this->_cmsWysiwygImages->getStorageRoot() . self::THUMBS_DIRECTORY_NAME;
    }

    /**
     * Simple way to check whether file is image or not based on extension
     *
     * @param string $filename
     * @return bool
     */
    public function isImage($filename)
    {
        if (!$this->hasData('_image_extensions')) {
            $this->setData('_image_extensions', $this->getAllowedExtensions('image'));
        }
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $this->_getData('_image_extensions'));
    }

    /**
     * Get resize width
     *
     * @return int
     */
    public function getResizeWidth()
    {
        return $this->_resizeParameters['width'];
    }

    /**
     * Get resize height
     *
     * @return int
     */
    public function getResizeHeight()
    {
        return $this->_resizeParameters['height'];
    }
}
