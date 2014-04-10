<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

use Magento\Phrase\Renderer\Placeholder;

class ErrorMessage
{
    /**
     * The error message.
     *
     * @var string
     */
    private $message;

    /**
     * The message substitution parameters.
     *
     * @var array
     */
    private $params;

    /**
     * The renderer to use for retrieving the log-compatible message.
     *
     * @var Placeholder
     */
    private $renderer;

    /**
     * Initialize the error message object.
     *
     * @param string $message Error message
     * @param array $params Message arguments (i.e. substitution parameters)
     */
    public function __construct($message, array $params = [])
    {
        $this->message = $message;
        $this->params = $params;
        $this->renderer = new Placeholder();
    }

    /**
     * Return the parameters associated with this error.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Return the message localized to based on the locale of the current request.
     *
     * @return string
     */
    public function getMessage()
    {
        return __($this->message, $this->params);
    }

    /**
     * Return the un-processed message, which can be used as a localization key by web service clients.
     *
     * @return string
     */
    public function getRawMessage()
    {
        return $this->message;
    }

    /**
     * Return the un-localized string, but with the parameters filled in.
     *
     * @return string
     */
    public function getLogMessage()
    {
        return $this->renderer->render([$this->message], $this->params);
    }
}
