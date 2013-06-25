<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

// work with URL, publicly accessible files (URL builder, path to URL converter)
class Mage_Core_Model_View_Url
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_View_Service
     */
    protected $_viewService;

    /**
     * @var Mage_Core_Model_View_Publisher
     */
    private $_publisher;

    /**
     * @var Mage_Core_Model_View_DeployedFilesManager
     */
    private $_deployedFilesManager;


    /**
     * View files URL model
     *
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_View_Service $viewService
     * @param Mage_Core_Model_View_Publisher $publisher
     * @param Mage_Core_Model_View_DeployedFilesManager $deployedFilesManager
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Mage_Core_Model_View_Service $viewService,
        Mage_Core_Model_View_Publisher $publisher,
        Mage_Core_Model_View_DeployedFilesManager $deployedFilesManager
    ) {
        $this->_filesystem = $filesystem;
        $this->_viewService = $viewService;
        $this->_publisher = $publisher;
        $this->_deployedFilesManager = $deployedFilesManager;
    }

    /**
     * Get url to file base on theme file identifier.
     * Publishes file there, if needed.
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = array())
    {
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        unset($params['_secure']);

        $publicFilePath = $this->getViewFilePublicPath($fileId, $params);
        $url = $this->getPublicFileUrl($publicFilePath, $isSecure);

        return $url;
    }

    /**
     * Publish file (if needed) and return its public path
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFilePublicPath($fileId, array $params = array())
    {
        $this->_viewService->updateDesignParams($params);
        $filePath = $this->_viewService->extractScope($fileId, $params);

        if ($this->_viewService->isViewFileOperationAllowed()) {
            $publicFilePath = $this->_publisher->getPublishedFilePath($filePath, $params);
        } else {
            $publicFilePath = $this->_deployedFilesManager->getDeployedFilePath($filePath, $params);
        }
        return $publicFilePath;
    }

    /**
     * Get url to public file
     *
     * @param string $publicFilePath
     * @param bool|null $isSecure
     * @return string
     * @throws Magento_Exception
     */
    public function getPublicFileUrl($publicFilePath, $isSecure = null)
    {
        foreach (array(
                Mage_Core_Model_Store::URL_TYPE_LIB     => Mage_Core_Model_Dir::PUB_LIB,
                Mage_Core_Model_Store::URL_TYPE_MEDIA   => Mage_Core_Model_Dir::MEDIA,
                Mage_Core_Model_Store::URL_TYPE_STATIC  => Mage_Core_Model_Dir::STATIC_VIEW,
                Mage_Core_Model_Store::URL_TYPE_CACHE   => Mage_Core_Model_Dir::PUB_VIEW_CACHE,
            ) as $urlType => $dirType
        ) {
            $dir = Mage::getBaseDir($dirType);
            if (strpos($publicFilePath, $dir) === 0) {
                $relativePath = ltrim(substr($publicFilePath, strlen($dir)), '\\/');
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                $url = Mage::getBaseUrl($urlType, $isSecure) . $relativePath;
                if ($this->_isStaticFilesSigned() && $this->_viewService->isViewFileOperationAllowed()) {
                    $fileMTime = $this->_filesystem->getMTime($publicFilePath);
                    $url .= '?' . $fileMTime;
                }
                return $url;
            }
        }
        throw new Magento_Exception(
            "Cannot build URL for the file '$publicFilePath' because it does not reside in a public directory."
        );
    }

    /**
     * Check if static files have to be signed
     *
     * @return bool
     */
    protected function _isStaticFilesSigned()
    {
        return (bool)Mage::getStoreConfig(Mage_Core_Model_Design_Package::XML_PATH_STATIC_FILE_SIGNATURE);
    }
}
