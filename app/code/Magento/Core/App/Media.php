<?php
/**
 * Media application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App;

use Magento\App\State,
    Magento\LauncherInterface,
    Magento\ObjectManager,
    Magento\Core\Model\File\Storage\Request,
    Magento\Core\Model\File\Storage\Response;

class Media implements LauncherInterface
{
    /**
     * @var \Magento\App\State
     */
    protected $_applicationState;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\File\Storage\Request
     */
    protected $_request;

    /**
     * Authorization function
     *
     * @var callable
     */
    protected $_isAllowed;

    /**
     * Media directory path
     *
     * @var string
     */
    protected $_mediaDirectory;

    /**
     * Configuration cache file path
     *
     * @var string
     */
    protected $_configCacheFile;

    /**
     * Requested file name relative to working directory
     *
     * @var string
     */
    protected $_relativeFileName;

    /**
     * Working directory
     *
     * @var string
     */
    protected $_workingDirectory;

    /**
     * @var \Magento\Core\Model\File\Storage\Response
     */
    protected $_response;

    /**
     * @var \Magento\App\Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Filesystem\Directory\Read $directory
     */
    protected $directory;

    /**
     * @param State $applicationState
     * @param ObjectManager $objectManager
     * @param Request $request
     * @param Response $response
     * @param callable $isAllowed
     * @param $workingDirectory
     * @param $mediaDirectory
     * @param $configCacheFile
     * @param $relativeFileName
     * @param \Magento\App\Filesystem $filesytem
     */
    public function __construct(
        State $applicationState,
        ObjectManager $objectManager,
        Request $request,
        Response $response,
        \Closure $isAllowed,
        $workingDirectory,
        $mediaDirectory,
        $configCacheFile,
        $relativeFileName,
        \Magento\App\Filesystem $filesystem
    ) {
        $this->_applicationState = $applicationState;
        $this->_objectManager = $objectManager;
        $this->_request = $request;
        $this->_response = $response;
        $this->_isAllowed = $isAllowed;
        $this->_workingDirectory = $workingDirectory;
        $this->_mediaDirectory = $mediaDirectory;
        $this->_configCacheFile = $configCacheFile;
        $this->_relativeFileName = $relativeFileName;
        $this->filesystem = $filesystem;
        $this->directory = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::MEDIA_DIR);
    }

    /**
     * Run application
     *
     * @return \Magento\App\ResponseInterface
     */
    public function launch()
    {
        try {
            if (!$this->_applicationState->isInstalled()) {
                $this->_response->setHttpResponseCode(404);
                return $this->_response;
            }
            if (!$this->_mediaDirectory) {
                $config = $this->_objectManager->create(
                    'Magento\Core\Model\File\Storage\Config', array('cacheFile' => $this->_configCacheFile)
                );
                $config->save();
                $this->_mediaDirectory = str_replace($this->_workingDirectory, '', $config->getMediaDirectory());
                $allowedResources = $config->getAllowedResources();
                $this->_relativeFileName = str_replace(
                    $this->_mediaDirectory . '/', '', $this->_request->getPathInfo()
                );
                $isAllowed = $this->_isAllowed;
                if (!$isAllowed($this->_relativeFileName, $allowedResources)) {
                    $this->_response->setHttpResponseCode(404);
                    return $this->_response;
                }
            }

            if (0 !== stripos($this->_request->getPathInfo(), $this->_mediaDirectory . '/')) {
                $this->_response->setHttpResponseCode(404);
                return $this->_response;
            }

            $sync = $this->_objectManager->get('Magento\Core\Model\File\Storage\Synchronization');
            $sync->synchronize($this->_relativeFileName, $this->_request->getFilePath());

            if ($this->directory->isReadable($this->directory->getRelativePath($this->_request->getFilePath()))) {
                $this->_response->setFilePath($this->_request->getFilePath());
            } else {
                $this->_response->setHttpResponseCode(404);
            }
            return $this->_response;
        } catch (\Magento\Core\Model\Store\Exception $e) {
            $this->_response->setHttpResponseCode(404);
            return $this->_response;
        }
    }
}
