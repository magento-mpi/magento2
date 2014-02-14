<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate\Inline;

interface ParserInterface
{
    /**
     * Default state for jason flag
     */
    const JSON_FLAG_DEFAULT_STATE = false;

    /**
     * Parse and save edited translation
     *
     * @param array $translateParams
     * @return $this
     */
    public function processAjaxPost(array $translateParams);

    /**
     * Replace html body with translation wrapping.
     *
     * @param string $body
     * @return string
     */
    public function processResponseBodyString($body);

    /**
     * Returns the body content that is being parsed.
     *
     * @return string
     */
    public function getContent();

    /**
     * Sets the body content that is being parsed passed upon the passed in string.
     *
     * @param $content string
     */
    public function setContent($content);

    /**
     * Set flag about parsed content is Json
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsJson($flag);
}
