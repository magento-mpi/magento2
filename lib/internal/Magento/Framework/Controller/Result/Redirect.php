<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Controller\Result;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * In many cases controller actions may result in a redirect
 * so this is a result object that implements all necessary properties of a HTTP redirect
 */
class Redirect implements ResultInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $code;

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
     * @param string $type
     * @return $this
     */
    public function setType($type = PERMANENT)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function renderResult(ResponseInterface $response)
    {
        $response->setRedirect($this->url, $this->code);
        return $this;
    }
}
