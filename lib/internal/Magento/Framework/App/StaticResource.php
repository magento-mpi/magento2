<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request;
use Magento\Framework\App\Response;

/**
 * Entry point for retrieving static resources like JS, CSS, images by requested public path
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StaticResource implements \Magento\Framework\AppInterface
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
     * @var \Magento\Framework\App\View\Asset\Publisher
     */
    private $publisher;

    /**
     * @var \Magento\Framework\View\Asset\Repository
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
     * @var \Magento\Framework\View\DesignInterface
     */
    private $design;

    /**
     * @param State $state
     * @param Response\FileInterface $response
     * @param Request\Http $request
     * @param \Magento\Framework\App\View\Asset\Publisher $publisher
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Module\ModuleList $moduleList
     * @param \Magento\ObjectManager $objectManager
     * @param ObjectManager\ConfigLoader $configLoader
     * @param \Magento\Framework\View\DesignInterface $design
     */
    public function __construct(
        State $state,
        Response\FileInterface $response,
        Request\Http $request,
        View\Asset\Publisher $publisher,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Module\ModuleList $moduleList,
        \Magento\ObjectManager $objectManager,
        ObjectManager\ConfigLoader $configLoader,
        \Magento\Framework\View\DesignInterface $design
    ) {
        $this->state = $state;
        $this->response = $response;
        $this->request = $request;
        $this->publisher = $publisher;
        $this->assetRepo = $assetRepo;
        $this->moduleList = $moduleList;
        $this->objectManager = $objectManager;
        $this->configLoader = $configLoader;
        $this->design = $design;
    }

    /**
     * Finds requested resource and provides it to the client
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    public function launch()
    {
        $appMode = $this->state->getMode();
        if ($appMode == \Magento\Framework\App\State::MODE_PRODUCTION) {
            $this->response->setHttpResponseCode(404);
        } else {
            try {
                $path = $this->request->get('resource');
                $params = $this->parsePath($path);
                $this->state->setAreaCode($params['area']);
                $this->objectManager->configure($this->configLoader->load($params['area']));
                $this->design->setDesignTheme($params['theme']);
                $file = $params['file'];
                unset($params['file']);
                $asset = $this->assetRepo->createAsset($file, $params);
                $this->response->setFilePath($asset->getSourceFile());
                $this->publisher->publish($asset);
            } catch (\Exception $e) {
                if ($appMode == \Magento\Framework\App\State::MODE_DEVELOPER) {
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
        $parts = explode('/', $path, 6);
        if (count($parts) < 5) {
            throw new \InvalidArgumentException("Requested path '$path' is wrong.");
        }
        $result = array();
        $result['area'] = $parts[0];
        $result['theme'] = $parts[1] . '/' . $parts[2];
        $result['locale'] = $parts[3];
        if (count($parts) >= 6 && $this->isModule($parts[4])) {
            $result['module'] = $parts[4];
        } else {
            $result['module'] = '';
            if (isset($parts[5])) {
                $parts[5] = $parts[4] . '/' . $parts[5];
            } else {
                $parts[5] = $parts[4];
            }
        }
        $result['file'] = $parts[5];
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
