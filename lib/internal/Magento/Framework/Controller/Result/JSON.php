<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Controller\Result;

use Magento\Framework\Controller\AbstractResult;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Translate\InlineInterface;

/**
 * A possible implementation of JSON response type (instead of hardcoding json_encode() all over the place)
 * Actual for controller actions that serve ajax requests
 */
class JSON extends AbstractResult
{
    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    protected $translateInline;

    /**
     * @var string
     */
    protected $json;

    /**
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     */
    public function __construct(InlineInterface $translateInline)
    {
        $this->translateInline = $translateInline;
    }

    /**
     * Set json data
     *
     * @param mixed $jsonData
     * @param boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param array $options Additional options used during encoding
     * @return $this
     */
    public function setJsonData($jsonData, $cycleCheck = false, $options = array())
    {
        $this->json = \Zend_Json::encode($jsonData, $cycleCheck, $options);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function render(ResponseInterface $response)
    {
        $response->representJson($this->translateInline->processResponseBody($this->json, true));
        return $this;
    }
}
