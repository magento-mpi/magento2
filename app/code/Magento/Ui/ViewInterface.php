<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

/**
 * Class ViewInterface
 */
interface ViewInterface
{
    /**
     * @param array $arguments
     * @param string $acceptType
     * @param array $requestParams
     * @return mixed|string
     */
    public function render(array $arguments = [], $acceptType = 'html', array $requestParams = []);

    /**
     * Getting template
     *
     * @return string|false
     */
    public function getTemplate();

    /**
     * Getting view data array
     *
     * @return array
     */
    public function getViewData();

    /**
     * Getting configuration settings array
     *
     * @return array
     */
    public function getViewConfiguration();

    /**
     * Getting JSON configuration data
     *
     * @return string
     */
    public function getConfigurationJson();

    /**
     * Getting instance name
     *
     * @return string
     */
    public function getName();
}
