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
 * A result that contains raw response - may be good for passing through files,
 * returning result of downloads or some other binary contents
 */
class Raw implements ResultInterface
{
    /**
     * @var string
     */
    protected $contents;

    /**
     * @var int
     */
    protected $httpResponseCode;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @param string $contents
     * @return $this
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
        return $this;
    }

    /**
     * @param int $httpCode
     * @return $this
     */
    public function setHttpResponseCode($httpCode)
    {
        $this->httpResponseCode = $httpCode;
        return $this;
    }

    /**
     * Set a header
     *
     * If $replace is true, replaces any headers already defined with that
     * $name.
     *
     * @param string $name
     * @param string $value
     * @param boolean $replace
     * @return $this
     */
    public function setHeader($name, $value, $replace = false)
    {
        $this->headers[] = [
            'name'    => $name,
            'value'   => $value,
            'replace' => $replace
        ];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function renderResult(ResponseInterface $response)
    {
        if (!empty($this->httpResponseCode)) {
            $response->setHttpResponseCode($this->httpResponseCode);
        }

        if (!empty($this->headers)) {
            foreach ($this->headers as $headerData) {
                $response->setHeader($headerData['name'], $headerData['value'], $headerData['replace']);
            }
        }

        $response->setBody($this->contents);
        return $this;
    }
}
