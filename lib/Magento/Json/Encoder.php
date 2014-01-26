<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Json;

use Magento\TranslateInterface;

/**
 * @package Magento\Json
 */
class Encoder implements EncoderInterface
{
    /**
     * Translator
     *
     * @var TranslateInterface
     */
    protected $translator;

    /**
     * @param TranslateInterface $translator
     */
    public function __construct(TranslateInterface $translator)
    {
        $this->translator = $translator;
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
        if ($this->translator->isAllowed()) {
            $this->translator->processResponseBody($json, true);
        }

        return $json;
    }
}