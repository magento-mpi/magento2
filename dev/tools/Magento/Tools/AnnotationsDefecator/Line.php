<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Tools\AnnotationsDefecator;

class Line implements FileItemI
{
    /**
     * Line content
     *
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $lineNumber;

    /**
     * @param string $content
     * @param int $lineNumber
     */
    public function __construct($content, $lineNumber)
    {
        $this->content = $content;
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->lineNumber;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->lineNumber = $number;
    }

    /**
     * @param int $number
     * @return bool
     */
    public function hasLineNumber($number)
    {
        return $number == $this->getNumber();
    }

    /**
     * Returns line content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getContentArray()
    {
        return [$this->content];
    }

    /**
     * Returns content indent
     *
     * @param string $content
     * @return int
     */
    public static function getContentIndent($content)
    {
        return strspn($content, ' ');
    }
}
