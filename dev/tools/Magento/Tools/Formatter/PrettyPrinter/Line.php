<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * This class handles a group of tokens, loosely called a line. Note that this can span multiple lines.
 * Class Line
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
class Line {
    /**
     * This member holds the actual tokens in the line
     * @var array
     */
    protected $tokens = array();

    /**
     * This method constructs the new line, adding the first token, if specified.
     * @param mixed $token Optional token to be added.
     */
    public function __construct($token = null) {
        // optionally add the token if one was specified
        if (null !== $token) {
            $this->add($token);
        }
    }

    /**
     * This method adds the token to the list of tokens.
     * @param mixed $token The token to be added.
     */
    public function add($token) {
        // just add the token to the end of the list
        $this->tokens[] = $token;
        // return this instance so that chaining can be accomplished
        return $this;
    }

    /**
     * This method returns the line as a string. Note that line breaks are dealt with.
     * @param string $prefix String containing the start of every new line
     */
    public function getLine($prefix) {
        $line = $prefix;
        // add each token to the string
        foreach ($this->tokens as $index=>$token) {
            if ($token instanceof LineBreak) {
                $line .= $token;
                // if there are more tokens, indent the next line
                if ($index < sizeof($this->tokens) - 1) {
                    $line .= $prefix;
                }
            } else {
                $line .= $token;
            }
        }
        return $line;
    }

    /**
     * This method translates this instance to a string.
     * @return string
     */
    public function __toString() {
        $line = '';
        // add each token to the string
        foreach ($this->tokens as $token) {
            $line .= $token;
        }
        return $line;
    }
}