<?php
/**
 * Log shell application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Config;

use Magento\Framework\App\Console\Response;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\AppInterface;
use Magento\Backend\Model\Config;

class UserConfig implements AppInterface
{
    /**
     * Config model accessor 
     *
     * @var Config
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
     * @param Config $configModel,
     * @param Response $response
     * @param array $request
     */
    public function __construct(
        Config $configModel,
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
        if (($configData['website'] !== null ) && ($configData['website'] !== null)
            && ($configData['website'] === $configData['store'])) {
            throwException('\'webiste\' and \'store\' both can not be 1 or 0 at the same time ');
        }
        foreach ($this->request['data'] as $key => $val) {
            $pathParts = explode('/', trim(str_replace('\\', '/', $key), '/'));
            if (count($pathParts) !== 3) {
                throwException(
                    'Only allowed path length for configuration data is 3, but you provided ' . count($pathParts)
                );
            }
            $configData['section'] = $pathParts[0];
            $groups = [];
            $groups[$pathParts[1]]['fields'][$pathParts[2]]['value'] = $val;
            $configData['groups'] = $groups;
            $this->configModel->addData($configData);
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
