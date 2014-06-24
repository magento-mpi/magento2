<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model\Angular;

class State
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $templateUrl;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $templateUrl
     */
    public function setTemplateUrl($templateUrl)
    {
        $this->templateUrl = $templateUrl;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTemplateUrl()
    {
        return $this->templateUrl;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    public function asJS()
    {
        return "{\n"
            . "url: '"         . $this->getUrl() . "',\n"
            . "templateUrl: '" . $this->getUrl() . "',\n"
            . "controller: '"  . $this->getController() . "Controller'\n"
            . "}\n";
    }
}
