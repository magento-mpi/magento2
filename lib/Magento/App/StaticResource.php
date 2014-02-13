<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class StaticResource implements \Magento\LauncherInterface
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var Response\Http
     */
    private $response;

    /**
     * @var Request\Http
     */
    private $request;

    /**
     * @var \Magento\View\Publisher
     */
    private $publisher;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    private $reader;

    /**
     * @var \Magento\Module\ModuleList
     */
    private $moduleList;

    /**
     * @var \Magento\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\File\Mime
     */
    private $mime;

    /**
     * @var \Magento\View\Service
     */
    private $viewService;

    /**
     * @param State $state
     * @param Response\Http $response
     * @param Request\Http $request
     * @param \Magento\View\Publisher $publisher
     * @param Filesystem|\Magento\Filesystem $filesystem
     * @param \Magento\Module\ModuleList $moduleList
     * @param \Magento\File\Mime $mime
     * @param \Magento\View\Service $viewService
     */
    public function __construct(
        State $state,
        Response\Http $response,
        Request\Http $request,
        \Magento\View\Publisher $publisher,
        \Magento\Filesystem $filesystem,
        \Magento\Module\ModuleList $moduleList,
        \Magento\File\Mime $mime,
        \Magento\View\Service $viewService
    ) {
        $this->state = $state;
        $this->response = $response;
        $this->request = $request;
        $this->publisher = $publisher;
        $this->filesystem = $filesystem;
        $this->reader = $filesystem->getDirectoryRead(\Magento\App\Filesystem::PUB_DIR);
        $this->moduleList = $moduleList;
        $this->mime = $mime;
        $this->viewService = $viewService;
    }

    /**
     * Finds requested resource and provides it to the client
     *
     * @return \Magento\App\ResponseInterface
     */
    public function launch()
    {
        if ($this->state->getMode() == \Magento\App\State::MODE_PRODUCTION) {
            $this->response->setHttpResponseCode(404);
        } else {
            $path = $this->request->get('resource');
            $params = $this->parsePath($path);
            $file = $params['file'];
            unset($params['file']);

            $this->state->setAreaCode($params['area']);
            $this->viewService->updateDesignParams($params);

            // todo: separate getting file path and publication
            $publicFile = $this->publisher->getPublicFilePath($file, $params);

            $content = $this->reader->readFile($this->reader->getRelativePath($publicFile));
            $this->response->setBody($content);

            $contentType = $this->mime->getMimeType($publicFile);
            $this->response->setHeader('Content-Type', $contentType);
        }
        return $this->response;
    }

    /**
     * Parse path to identify parts needed for searching original file
     *
     * @param string $path
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function parsePath($path)
    {
        $path = ltrim($path, '/');
        $parts = explode('/', $path, 5);
        if (count($parts) < 4) {
            throw new \InvalidArgumentException("Requested path '$path' is wrong.");
        }
        $result = array();
        $result['area'] = $parts[0];
        $result['theme'] = $parts[1];
        $result['locale'] = $parts[2];
        if (count($parts) >= 5 && $this->isModule($parts[3])) {
            $result['module'] = $parts[3];
        } else {
            $result['module'] = '';
            if (isset($parts[4])) {
                $parts[4] = $parts[3] . '/' . $parts[4];
            } else {
                $parts[4] = $parts[3];
            }
        }
        $result['file'] = $parts[4];
        return $result;
    }

    /**
     * Check if active module 'name' exists
     *
     * @param string $name
     * @return bool
     */
    protected function isModule($name)
    {
        return isset($this->moduleList->getModules()[$name]);
    }
}
