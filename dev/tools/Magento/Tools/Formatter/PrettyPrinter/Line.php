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
    const ATTRIBUTE_LINE = 'line';
    const ATTRIBUTE_TERMINATOR = 'terminator';
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
     * This method returns the tokens that make up the line.
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * This member sets the tokens for this line to the passed in values.
     */
    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * This method returns the representation of the line for
     * @param int $level Indicator for the level you want the lines split at
     * @return array
     */
    public function splitLine($level)
    {
        $lineBreakTokens = array();
        // first, count the number of line break instances in the line
        foreach ($this->tokens as $token) {
            if ($token instanceof LineBreak) {
                $id = $this->getLineBreakId($token);
                if (!array_key_exists($id, $lineBreakTokens)) {
                    $lineBreakTokens[$id] = array();
                }

                if (array_key_exists('total', $lineBreakTokens[$id])) {
                    $lineBreakTokens[$id]['total']++;
                } else {
                    $lineBreakTokens[$id]['total'] = 1;
                    $lineBreakTokens[$id]['index'] = 0;
                }
            }
        }
        // stores the array of arrays
        $currentLines = array();
        $index = 0;
        // build up the string by compiling the tokens
        foreach ($this->tokens as $token) {
            // if no current line, create one and put it in the array
            if ($index >= sizeof($currentLines)) {
                $currentLines[$index] = array();
                $currentLines[$index][self::ATTRIBUTE_LINE] = '';
            }
            if (is_string($token)) {
                // add the current token to the end of the current line
                $currentLines[$index][self::ATTRIBUTE_LINE] .= $token;
            } elseif ($token instanceof LineBreak) {
                $id = $this->getLineBreakId($token);
                $resolvedToken = $token->getValue(
                    $level,
                    $lineBreakTokens[$id]['index']++,
                    $lineBreakTokens[$id]['total']
                );
                if ($token instanceof HardLineBreak) {
                    $currentLines[$index][self::ATTRIBUTE_TERMINATOR] = $token;
                    $index++;
                } else if ($resolvedToken instanceof HardLineBreak) {
                    $currentLines[$index][self::ATTRIBUTE_TERMINATOR] = $resolvedToken;
                    $index++;
                } else {
                    $currentLines[$index][self::ATTRIBUTE_LINE] .= (string)$resolvedToken;
                }
            }
        }
        return $currentLines;
    }

    /**
     * This method returns the id for the specified line break.
     * @param LineBreak $lineBreak
     * @return string
     */
    private function getLineBreakId(LineBreak $lineBreak)
    {
        if ($lineBreak->isGroupedByClass()) {
            $id = get_class($lineBreak);
        } else {
            $id = spl_object_hash($lineBreak);
        }
        return $id;
    }
}
