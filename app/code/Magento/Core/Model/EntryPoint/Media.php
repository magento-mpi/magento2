<?php
/**
 * Media downloader entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\EntryPoint;

class Media extends \Magento\Core\Model\AbstractEntryPoint
{
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
     * @param \Magento\Core\Model\Config\Primary $config
     * @param \Magento\Core\Model\File\Storage\Request $request
     * @param callable $isAllowed
     * @param string $workingDirectory
     * @param string $mediaDirectory
     * @param string $configCacheFile
     * @param string $relativeFileName
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\File\Storage\Response
     */
    public function __construct(
        \Magento\Core\Model\Config\Primary $config,
        \Magento\Core\Model\File\Storage\Request $request,
        \Closure $isAllowed,
        $workingDirectory,
        $mediaDirectory,
        $configCacheFile,
        $relativeFileName,
        \Magento\ObjectManager $objectManager = null,
        \Magento\Core\Model\File\Storage\Response $response = null
    ) {
        parent::__construct($config, $objectManager);
        $this->_request = $request;
        $this->_isAllowed = $isAllowed;
        $this->_workingDirectory = $workingDirectory;
        $this->_mediaDirectory = $mediaDirectory;
        $this->_configCacheFile = $configCacheFile;
        $this->_relativeFileName = $relativeFileName;
        $this->_response = $response ?: new \Magento\Core\Model\File\Storage\Response($this->_objectManager);
    }

    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        try {
            $appState = $this->_objectManager->get('Magento\Core\Model\App\State');
            if (!$appState->isInstalled()) {
                $this->_response->sendNotFound();
                return;
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
                    $this->_response->sendNotFound();
                    return;
                }
            }

            if (0 !== stripos($this->_request->getPathInfo(), $this->_mediaDirectory . '/')) {
                $this->_response->sendNotFound();
                return;
            }

            $sync = $this->_objectManager->get('Magento\Core\Model\File\Storage\Synchronization');
            $sync->synchronize($this->_relativeFileName, $this->_request->getFilePath());

            if (is_readable($this->_request->getFilePath())) {
                $this->_response->sendFile($this->_request->getFilePath());
            } else {
                $this->_response->sendNotFound();
            }
        } catch (\Magento\Core\Model\Store\Exception $e) {
            $this->_response->sendNotFound();
            return;
        }
    }
}
