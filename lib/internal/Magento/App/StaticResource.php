<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

/**
 * Entry point for retrieving static resources like JS, CSS, images by requested public path
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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
     * @var \Magento\App\View\Asset\Publisher
     */
    private $publisher;

    /**
     * @var \Magento\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Magento\Module\ModuleList
     */
    private $moduleList;

    /**
     * @var \Magento\ObjectManager
     */
    private $objectManager;

    /**
     * @var ObjectManager\ConfigLoader
     */
    private $configLoader;

    /**
     * @param State $state
     * @param Response\FileInterface $response
     * @param Request\Http $request
     * @param \Magento\App\View\Asset\Publisher $publisher
     * @param \Magento\View\Asset\Repository $assetRepo
     * @param \Magento\Module\ModuleList $moduleList
     * @param \Magento\ObjectManager $objectManager
     * @param ObjectManager\ConfigLoader $configLoader
     */
    public function __construct(
        State $state,
        Response\FileInterface $response,
        Request\Http $request,
        \Magento\App\View\Asset\Publisher $publisher,
        \Magento\View\Asset\Repository $assetRepo,
        \Magento\Module\ModuleList $moduleList,
        \Magento\ObjectManager $objectManager,
        ObjectManager\ConfigLoader $configLoader
    ) {
        $this->state = $state;
        $this->response = $response;
        $this->request = $request;
        $this->publisher = $publisher;
        $this->assetRepo = $assetRepo;
        $this->moduleList = $moduleList;
        $this->objectManager = $objectManager;
        $this->configLoader = $configLoader;
    }

    /**
     * Finds requested resource and provides it to the client
     *
     * @return \Magento\App\ResponseInterface
     * @throws \Exception
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
            $this->objectManager->configure($this->configLoader->load($params['area']));
            $file = $params['file'];
            unset($params['file']);

            try {
                $asset = $this->assetRepo->createAsset($file, $params);
                $this->response->setFilePath($asset->getSourceFile());
                $this->publisher->publish($asset);
            } catch (\Exception $e) {
                if ($appMode == \Magento\App\State::MODE_DEVELOPER) {
                    throw $e;
                }
                $this->response->setHttpResponseCode(404);
            }
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
        return null !== $this->moduleList->getModule($name);
    }
}
