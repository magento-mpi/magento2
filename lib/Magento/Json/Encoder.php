<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Json;

class Encoder implements EncoderInterface
{
    /**
     * Translator
     *
     * @var \Magento\Translate\InlineInterface
     */
    protected $translateInline;

    /**
     * @param \Magento\Translate\InlineInterface $translateInline
     */
    public function __construct(\Magento\Translate\InlineInterface $translateInline)
    {
        $this->translateInline = $translateInline;
    }

    /**
     * Encode the mixed $data into the JSON format.
     *
     * @param mixed $data
     * @return string
     */
    public function encode($data)
    {
        $json = \Zend_Json::encode($data);
        if ($this->translateInline->isAllowed()) {
            $this->translateInline->processResponseBody($json, true);
        }

        return $json;
    }
}