<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Controller\Result;

use Magento\Framework\Controller\AbstractResult;
use Magento\Framework\App;
use Magento\Backend\Model\UrlInterface;

/**
 * In many cases controller actions may result in a redirect
 * so this is a result object that implements all necessary properties of a HTTP redirect
 */
class Redirect extends AbstractResult
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $url;

    /**
     * Constructor
     *
     * @param App\Response\RedirectInterface $redirect
     * @param \Magento\Backend\Model\UrlInterface $urlBuilder
     */
    public function __construct(
        App\Response\RedirectInterface $redirect,
        UrlInterface $urlBuilder
    ) {
        $this->redirect = $redirect;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Set url from referer
     *
     * @return $this
     */
    public function setRefererUrl()
    {
        $this->url = $this->redirect->getRefererUrl();
        return $this;
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
     * Set url by path
     *
     * @param string $path
     * @param array $params
     * @return $this
     */
    public function setPath($path, array $params = [])
    {
        $this->url = $this->urlBuilder->getUrl($path, $this->redirect->updatePathParams($params));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function render(App\ResponseInterface $response)
    {
        $response->setRedirect($this->url);
        return $this;
    }
}
