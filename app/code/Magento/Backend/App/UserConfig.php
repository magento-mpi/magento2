<?php
/**
 * Application for managing user configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App;

use Magento\Framework\App\Console\Response;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\AppInterface;
use Magento\Backend\Model\Config as BackendModelConfig;

class UserConfig implements AppInterface
{
    /**
     * Config model accessor 
     *
     * @var BackendModelConfig
     */
    private $configModel;

    /**
     * Console response
     *
     * @var Response
     */
    private $response;

    /**
     * Requested changes
     *
     * @var array
     */
    private $request;

    /**
     * Constructor
     *
     * @param BackendModelConfig $configModel,
     * @param Response $response
     * @param array $request
     */
    public function __construct(
        BackendModelConfig $configModel,
        Response $response,
        array $request
    ) {
        $this->configModel = $configModel;
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Run application
     *
     * @return \Magento\Framework\App\ResponseInterface     *
     */
    public function launch()
    {
        $this->response->terminateOnSend(false);
        $this->updateUserConfigData();
        return $this->response;
    }

    /**
     * Inserts provided user configuration data into database
     *
     * @return void
     * @throws \Exception
     */
    private function updateUserConfigData()
    {
        $configData = [];
        $configData['website'] = isset($this->request['website']) ? $this->request['website'] : null;
        $configData['store'] = isset($this->request['store']) ? $this->request['store'] : null;
        if (($configData['website'] !== null) && ($configData['website'] === $configData['store'])) {
            throw new \UnexpectedValueException("'website' and 'store' refer to the same ID");
        }
        foreach ($this->request['data'] as $key => $val) {
            $this->configModel->setDataByPath($key, $val);
            $this->configModel->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function catchException(Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
}
