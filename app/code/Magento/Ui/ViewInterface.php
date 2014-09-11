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
     * @return mixed|string
     */
    public function render(array $arguments = [], $acceptType = 'html');

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

    /**
     * Getting parent name component instance
     *
     * @return string
     */
    public function getParentName();

    /**
     * Add data into configuration element view
     *
     * @param AbstractView $view
     * @param array $data
     */
    public function addConfigData(AbstractView $view, array $data);
}
