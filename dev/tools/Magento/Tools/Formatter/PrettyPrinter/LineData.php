<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * This class represents both the syntax and the line.
 * Class LineData
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
class LineData
{
    /**
     * This member holds the line generated from the syntax.
     * @var Line
     */
    public $line;

    /**
     * This member holds the syntax for the line. This may be null if the line was generated from
     * another line (i.e. was split).
     * @var AbstractSyntax
     */
    public $syntax = null;

    /**
     * This method constructs a new instance with the given information.
     * @param AbstractSyntax|null $syntax Syntax for the given line
     * @param Line $line Initial value for the line.
     */
    public function __construct(AbstractSyntax $syntax = null, Line $line = null)
    {
        $this->syntax = $syntax;
        $this->line = $line;
    }
}
