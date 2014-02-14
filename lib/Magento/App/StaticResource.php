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
     * @var Response\FileInterface
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
     * @var \Magento\Module\ModuleList
     */
    private $moduleList;

    /**
     * @var \Magento\View\FileSystem
     */
    private $filesystem;

    /**
     * @var \Magento\View\Service
     */
    private $viewService;

    /**
     * @param State $state
     * @param Response\FileInterface $response
     * @param Request\Http $request
     * @param \Magento\View\Publisher $publisher
     * @param \Magento\View\FileSystem $filesystem
     * @param \Magento\Module\ModuleList $moduleList
     * @param \Magento\View\Service $viewService
     */
    public function __construct(
        State $state,
        Response\FileInterface $response,
        Request\Http $request,
        \Magento\View\Publisher $publisher,
        \Magento\View\FileSystem $filesystem,
        \Magento\Module\ModuleList $moduleList,
        \Magento\View\Service $viewService
    ) {
        $this->state = $state;
        $this->response = $response;
        $this->request = $request;
        $this->publisher = $publisher;
        $this->filesystem = $filesystem;
        $this->moduleList = $moduleList;
        $this->viewService = $viewService;
    }

    /**
     * Finds requested resource and provides it to the client
     *
     * @return \Magento\App\ResponseInterface
     */
    public function launch()
    {
        $appMode = $this->state->getMode();
        if ($appMode == \Magento\App\State::MODE_PRODUCTION) {
            $this->response->setHttpResponseCode(404);
        } else {
            $path = $this->request->get('resource');
            $params = $this->parsePath($path);
            $file = $params['file'];
            unset($params['file']);

            $this->state->setAreaCode($params['area']);

            if ($appMode == \Magento\App\State::MODE_DEVELOPER) {
                $publicFile = $this->filesystem->getViewFile($file, $params);
            } else {
                $this->viewService->updateDesignParams($params);
                $publicFile = $this->publisher->getPublicFilePath($file, $params);
            }

            $this->response->setFilePath($publicFile);
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
