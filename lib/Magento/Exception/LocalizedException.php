<?php
/**
 * Localized Exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

use Magento\Phrase\Renderer\Placeholder;

class LocalizedException extends \Exception
{
    /** @var array */
    protected $params = [];

    /** @var string */
    protected $rawMessage;

    /** @var Placeholder */
    private $renderer;

    /**
     * @param string     $message
     * @param array      $params
     * @param \Exception $cause
     */
    public function __construct($message, array $params = [], \Exception $cause = null)
    {
        $this->params = $params;
        $this->rawMessage = $message;
        $this->renderer = new Placeholder();

        parent::__construct(__($message, $params), 0, $cause);
    }

    /**
     * Get the un-processed message, without the parameters filled in
     *
     * @return string
     */
    public function getRawMessage()
    {
        return $this->rawMessage;
    }

    /**
     * Get the un-localized message, but with the parameters filled in
     *
     * @return string
     */
    public function getLogMessage()
    {
        return $this->renderer->render([$this->rawMessage], $this->params);
    }

    /**
     * Returns the array of parameters in the message
     *
     * @return array Parameter name => values
     */
    public function getParameters()
    {
        return $this->params;
    }
}
