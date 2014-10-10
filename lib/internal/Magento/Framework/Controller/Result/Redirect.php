<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Controller\Result;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App;

/**
 * In many cases controller actions may result in a redirect
 * so this is a result object that implements all necessary properties of a HTTP redirect
 */
class Redirect implements ResultInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @param App\Response\RedirectInterface $redirect
     */
    public function __construct(App\Response\RedirectInterface $redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function renderResult(App\ResponseInterface $response)
    {
        $this->redirect->redirect($response, $this->url, $this->arguments);
        return $this;
    }
}
