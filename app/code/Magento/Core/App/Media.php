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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\App;
use Magento\Framework\AppInterface;
use Magento\Framework\ObjectManager;
use Magento\Core\Model\File\Storage\Request;
use Magento\Core\Model\File\Storage\Response;

class Media implements AppInterface
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\File\Storage\Request
     */
    protected $_request;

    /**
     * Authorization function
     *
     * @var \Closure
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
     * @var \Magento\Framework\App\Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read $directory
     */
    protected $directory;

    /**
     * @param ObjectManager $objectManager
     * @param Request $request
     * @param Response $response
     * @param \Closure $isAllowed
     * @param string $workingDirectory
     * @param string $mediaDirectory
     * @param string $configCacheFile
     * @param string $relativeFileName
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(
        ObjectManager $objectManager,
        Request $request,
        Response $response,
        \Closure $isAllowed,
        $workingDirectory,
        $mediaDirectory,
        $configCacheFile,
        $relativeFileName,
        \Magento\Framework\App\Filesystem $filesystem
    ) {
        $this->_objectManager = $objectManager;
        $this->_request = $request;
        $this->_response = $response;
        $this->_isAllowed = $isAllowed;
        $this->_workingDirectory = $workingDirectory;
        $this->_mediaDirectory = $mediaDirectory;
        $this->_configCacheFile = $configCacheFile;
        $this->_relativeFileName = $relativeFileName;
        $this->filesystem = $filesystem;
        $this->directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * Run application
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \LogicException
     */
    public function launch()
    {
        if (!$this->_mediaDirectory) {
            $config = $this->_objectManager->create(
                'Magento\Core\Model\File\Storage\Config',
                array('cacheFile' => $this->_configCacheFile)
            );
            $config->save();
            $this->_mediaDirectory = str_replace($this->_workingDirectory, '', $config->getMediaDirectory());
            $allowedResources = $config->getAllowedResources();
            $this->_relativeFileName = str_replace(
                $this->_mediaDirectory . '/',
                '',
                $this->_request->getPathInfo()
            );
            $isAllowed = $this->_isAllowed;
            if (!$isAllowed($this->_relativeFileName, $allowedResources)) {
                throw new \LogicException('The specified path is not allowed.');
            }
        }

        if (0 !== stripos($this->_request->getPathInfo(), $this->_mediaDirectory . '/')) {
            throw new \LogicException('The specified path is not within media directory.');
        }

        $sync = $this->_objectManager->get('Magento\Core\Model\File\Storage\Synchronization');
        $sync->synchronize($this->_relativeFileName, $this->_request->getFilePath());

        if ($this->directory->isReadable($this->directory->getRelativePath($this->_request->getFilePath()))) {
            $this->_response->setFilePath($this->_request->getFilePath());
        } else {
            $this->_response->setHttpResponseCode(404);
        }
        return $this->_response;
    }

    /**
     * {@inheritdoc}
     */
    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        $this->_response->setHttpResponseCode(404);
        $this->_response->sendHeaders();
        return true;
    }
}
