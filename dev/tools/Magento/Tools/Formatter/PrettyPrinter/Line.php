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
class Line
{
    /**
     * This member holds the actual tokens in the line
     * @var array
     */
    protected $tokens = array();

    /**
     * This method constructs the new line, adding the first token, if specified.
     * @param mixed $token Optional token to be added.
     */
    public function __construct($token = null)
    {
        // optionally add the token if one was specified
        if (null !== $token) {
            $this->add($token);
        }
    }

    /**
     * This method translates this instance to a string.
     * @return string
     */
    public function __toString()
    {
        return $this->getLine();
    }

    /**
     * This method adds the token to the list of tokens.
     * @param mixed $token The token to be added.
     */
    public function add($token)
    {
        // just add the token to the end of the list
        if (is_array($token)) {
            $this->tokens = array_merge($this->tokens, $token);
        } else {
            $this->tokens[] = $token;
        }
        // return this instance so that chaining can be accomplished
        return $this;
    }

    /**
     * This method returns the line as a string.
     */
    public function getLine()
    {
        return implode('', $this->tokens);
    }

    /**
     * This member sets the tokens for this line to the passed in values.
     */
    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * This member returns a tree
     * @param $prefix
     * @param $level
     */
    public function splitLines($level = 0)
    {
        // stores the array of arrays
        $currentLines = array();
        $index = 0;
        // loop through the elements to determine ending lines
        foreach ($this->tokens as $token) {
            // if no current line, create one and put it in the array
            if ($index >= sizeof($currentLines)) {
                $currentLines[$index] = array();
            }
            // add the current token to the end of the current line
            array_push($currentLines[$index], $token);
            // if this is a terminating token, flag that a new line needs to be generated
            if ($token instanceof LineBreak && $level >= $token->getLevel()) {
                $index++;
            }
        }
        return $currentLines;
    }
}
