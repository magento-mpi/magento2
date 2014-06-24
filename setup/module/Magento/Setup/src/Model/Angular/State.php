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
    protected $title;

    /**
     * @var string
     */
    protected $id;

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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
            . "templateUrl: '" . $this->getTemplateUrl() . "',\n"
            . "controller: '"  . $this->getController() . "Controller'\n"
            . "}\n";
    }
}
