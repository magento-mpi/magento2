<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Console;

/**
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
class Response implements \Magento\Framework\App\ResponseInterface
{
    /**
     * Status code
     * Possible values:
     *  0 (successfully)
     *  1-255 (error)
     *  -1 (error)
     *
     * @var int
     */
    protected $code = 0;

    /**
     * @var string
     */
    private $body;

    /**
     * Set whether to terminate process on send or not
     *
     * @var bool
     */
    protected $terminateOnSend = true;

    /**
     * Send response to client
     * @return int
     */
    public function sendResponse()
    {
        if (!empty($this->body)) {
            echo $this->body;
        }
        if ($this->terminateOnSend) {
            exit($this->code);
        }
        return $this->code;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param int $code
     * @return void
     */
    public function setCode($code)
    {
        if ($code > 255) {
            $code = 255;
        }
        $this->code = $code;
    }

    /**
     * Set whether to terminate process on send or not
     *
     * @param bool $terminate
     * @return void
     */
    public function terminateOnSend($terminate)
    {
        $this->terminateOnSend = $terminate;
    }
}
