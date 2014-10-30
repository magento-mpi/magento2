<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\AnnotationsDefecator;

class Annotation implements FileItemI
{
    /**
     * Annotation wrappers, head and foot, hat and shoes
     *
     * @var array
     */
    static public $wrappers = [
        '/**',
        '*/',
        '/*'
    ];

    /**
     * @var string
     */
    static public $contentMarker = '* ';

    /**
     * @var array
     */
    private $content = [];

    /**
     * @var array
     */
    private $wrappersContent = [];

    /**
     * @var int
     */
    private $indent = 0;

    /**
     * @return int
     */
    public function getNumber()
    {
        return current($this->wrappersContent)->getNumber();
    }

    /**
     * @return int
     */
    public function getLastNumber()
    {
        if (isset($this->wrappersContent[1])) {
            return $this->wrappersContent[1]->getNumber();
        }

        return $this->content[count($this->content)-1]->getNumber();
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        if (isset($this->wrappersContent[0])) {
            $this->wrappersContent[0]->setNumber($number);
            $number++;
        }

        /** @var FileItemI $contentItem */
        foreach ($this->content as $contentItem) {
            $contentItem->setNumber($number);
            $number++;
        }
        if (isset($this->wrappersContent[1])) {
            $this->wrappersContent[1]->setNumber($number);
        }
    }
    /**
     * @param int $number
     * @return bool
     */
    public function hasLineNumber($number)
    {
        $startNumber = $this->getNumber();
        $endNumber = $this->getLastNumber();

        return $startNumber <= $number and $number <= $endNumber;
    }

    /**
     * Returns line content
     *
     * @return string
     */
    public function getContent()
    {
        return implode('', $this->getContentArray());
    }

    /**
     * @return array
     */
    public function getContentArray()
    {
        $aggregatedContent = [];

        if (isset($this->wrappersContent[0])) {
            $aggregatedContent[] = $this->wrappersContent[0]->getContent();
        }
        /** @var Line $line */
        foreach ($this->content as $line) {
            $aggregatedContent[] = $line->getContent();
        }

        if (isset($this->wrappersContent[1])) {
            $aggregatedContent[] = $this->wrappersContent[1]->getContent();
        }

        return $aggregatedContent;
    }

    /**
     * Add annotations content
     *
     * @param Line $content
     */
    public function addLine(Line $content)
    {
        if (Annotation::isAnnotationWrapper($content->getContent())) {
            $this->wrappersContent[] = $content;
        } else {
            $this->content[] = $content;
            if (!$this->getContentIndent()) {
                $this->setContentIndent(Line::getContentIndent($content->getContent()));
            }
        }
    }

    /**
     * Sets content indent
     *
     * @param int $indent
     */
    public function setContentIndent($indent)
    {
        $this->indent = $indent;
    }

    /**
     * Returns content indent
     *
     * @return int
     */
    public function getContentIndent()
    {
        return $this->indent;
    }

    /**
     * Adds string content to annotation
     *
     * @param string $content
     */
    public function addContent($content)
    {
        if (empty($this->wrappersContent)) {
            $this->_initWrappers();
        }
        $this->content[] = $this->_initLine(self::$contentMarker . $content, $this->getContentIndent());
    }

    /**
     * Initialize wrappers with indent
     */
    private function _initWrappers()
    {
        $this->wrappersContent[] = $this->_initLine(self::$wrappers[0], $this->getContentIndent() - 1);
        $this->wrappersContent[] = $this->_initLine(self::$wrappers[1], $this->getContentIndent());

    }

    /**
     * Initialize Line with indent
     *
     * @param string $content
     * @param int $toIndent
     * @return Line
     */
    private function _initLine($content, $toIndent)
    {
        $prefix = $toIndent > 0 ? str_repeat(' ', $toIndent) : '';
        return new Line($prefix . $content, 0);
    }

    /**
     * Checks line content for being annotation wrapped
     *
     * @param string $content
     * @return bool
     */
    public static function isAnnotationWrapper($content)
    {
        if (preg_match('/([\w])/', $content)) {
            return false;
        }
        $wrapperIn = strpos($content, Annotation::$wrappers[0]) !== false
            || strpos($content, Annotation::$wrappers[2]) !== false;
        $wrapperOut = strpos($content, Annotation::$wrappers[1]) !== false;
        if ($wrapperIn and $wrapperOut) {
            return false;
        }
        if ($wrapperIn or $wrapperOut) {
            return true;
        }
        return false;
    }
}
