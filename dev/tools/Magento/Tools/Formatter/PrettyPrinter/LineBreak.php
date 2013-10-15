<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

abstract class LineBreak
{
    /**
     * This member holds a flag indicating if the alternate break value should be displayed.
     * @var boolean
     */
    protected $alternate = true;

    /**
     * This member returns the level which the line break represents. It is up to the break itself to return an
     * appropriate value with respect to other breaks in the line.
     * @return int
     */
    abstract public function getLevel();

    /**
     * This method returns if the next line should be indented.
     */
    abstract public function isNextLineIndented();

    /**
     * This method sets the display of the alternate value.
     * @param boolean $alternate
     */
    public function setAlternate($alternate)
    {
        $this->alternate = $alternate;
    }
}
