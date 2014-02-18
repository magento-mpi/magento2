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
     * @var \Magento\View\FileResolver
     */
    private $fileResolver;

    /**
     * @var \Magento\Module\ModuleList
     */
    private $moduleList;

    /**
     * @var \Magento\View\Design\Theme\ListInterface
     */
    private $themeList;

    /**
     * @param State $state
     * @param Response\FileInterface $response
     * @param Request\Http $request
     * @param \Magento\View\FileResolver $resolver
     * @param \Magento\Module\ModuleList $moduleList
     * @param \Magento\View\Design\Theme\ListInterface $themeList
     */
    public function __construct(
        State $state,
        Response\FileInterface $response,
        Request\Http $request,
        \Magento\View\FileResolver $resolver,
        \Magento\Module\ModuleList $moduleList,
        \Magento\View\Design\Theme\ListInterface $themeList
    ) {
        $this->state = $state;
        $this->response = $response;
        $this->request = $request;
        $this->fileResolver = $resolver;
        $this->moduleList = $moduleList;
        $this->themeList = $themeList;
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
            $this->state->setAreaCode($params['area']);

            $file = $params['file'];
            $params['themeModel'] = $this->getThemeModel($params['area'], $params['theme']);
            unset($params['file'], $params['theme']);

            if ($appMode == \Magento\App\State::MODE_DEVELOPER) {
                $resourceFile = $this->fileResolver->getViewFile($file, $params);
            } else {
                $resourceFile = $this->fileResolver->getPublicViewFile($file, $params);
            }

            $this->response->setFilePath($resourceFile);
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
     * Get theme model by its code for specified area
     *
     * @param string $area
     * @param string $themeCode
     * @return \Magento\View\Design\ThemeInterface
     * @throws \UnexpectedValueException
     */
    protected function getThemeModel($area, $themeCode)
    {
        $themeModel = $this->themeList->getThemeByFullPath($area
            . \Magento\View\Design\ThemeInterface::PATH_SEPARATOR . $themeCode);
        if (!$themeModel) {
            throw new \UnexpectedValueException("Can't find theme '$themeCode' for area '$area'");
        }
        return $themeModel;
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
