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
 */
class Line
{
    const ATTRIBUTE_INDEX = 'index';

    const ATTRIBUTE_SORT_ORDER = 'sortOrder';

    const ATTRIBUTE_TOTAL = 'total';

    /**
     * This member holds the line break token information, which is compiled information about the
     * occurrences of linebreak in the current token list.
     *
     * @var array
     */
    protected $lineBreakTokens = array();

    /**
     * This member holds the actual tokens in the line
     *
     * @var array
     */
    protected $tokens = array();

    /**
     * This method constructs the new line, adding the first token, if specified.
     *
     * @param array|string|LineConditionInterface|ConditionalLineBreak $token Optional token to be added.
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
     *
     * @param array|string|LineConditionInterface|ConditionalLineBreak $token The token to be added.
     * @return Line
     */
    public function add($token)
    {
        if (is_array($token)) {
            // add each element in the array to line so that line break information can be persisted
            foreach ($token as $itemToken) {
                $this->add($itemToken);
            }
        } else {
            // slight optimization; if last element was a string and this element is a string, combine them
            $tokenCount = sizeof($this->tokens);
            if ($tokenCount > 0 && is_string($this->tokens[$tokenCount - 1]) && is_string($token)) {
                $this->tokens[$tokenCount - 1] .= $token;
            } else {
                $lastToken = $this->getLastToken();
                // if the last token depends on the next value, give it a chance to operate
                if ($lastToken instanceof LineConditionInterface) {
                    $token = $lastToken->processToken($this->tokens, $token);
                }
                if (null !== $token) {
                    // just add the token to the end of the list
                    $this->tokens[] = $token;
                    // persist line break information
                    if ($token instanceof ConditionalLineBreak) {
                        $this->saveLineBreakToken($token);
                    }
                }
            }
        }
        // return this instance so that chaining can be accomplished
        return $this;
    }

    /**
     * This method returns the first token in the list.
     *
     * @return array|string|LineConditionInterface|ConditionalLineBreak
     */
    public function getFirstToken()
    {
        return sizeof($this->tokens) > 0 ? $this->tokens[0] : null;
    }

    /**
     * This method returns the last token in the list.
     *
     * @return array|string|LineConditionInterface|ConditionalLineBreak
     */
    public function getLastToken()
    {
        $index = sizeof($this->tokens) - 1;
        return $index >= 0 ? $this->tokens[$index] : null;
    }

    /**
     * This method returns the line as a string.
     *
     * @return string
     */
    public function getLine()
    {
        return implode('', $this->tokens);
    }

    /**
     * This method returns the line break tokens found in the line.
     *
     * @return array
     */
    public function getLineBreakTokens()
    {
        return $this->lineBreakTokens;
    }

    /**
     * This method returns a sorted array of line break sort order values.
     *
     * @return array
     */
    public function getSortedLineBreaks()
    {
        // determine all of the sort orders found in the line
        $sortOrders = array(0);
        foreach ($this->lineBreakTokens as $lineBreakToken) {
            $sortOrders[] = $lineBreakToken[Line::ATTRIBUTE_SORT_ORDER];
        }
        // only need the unique values
        // $sortOrders = array_unique($sortOrders, SORT_NUMERIC);
        // but need them in numerical order
        sort($sortOrders, SORT_NUMERIC);
        // return the new list
        return $sortOrders;
    }

    /**
     * This method returns the array of tokens that make up the line.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * This method returns if the no indent token if found in the string.
     *
     * @return bool
     */
    public function isNoIndent()
    {
        return $this->getFirstToken() instanceof IndentConsumer;
    }

    /**
     * This method replaces the last n characters of the line, where n is the size of the replacement.
     *
     * @param string $replacement String containing the replacement value.
     * @return void
     */
    public function replaceEndOfToken($replacement)
    {
        $tokenCount = sizeof($this->tokens);
        if ($tokenCount > 0 && is_string($this->tokens[$tokenCount - 1])) {
            $this->tokens[$tokenCount - 1] = substr(
                $this->tokens[$tokenCount - 1],
                0,
                strlen($this->tokens[$tokenCount - 1]) - strlen($replacement)
            ) . $replacement;
        }
    }

    /**
     * This member sets the tokens for this line to the passed in values.
     *
     * @param array|string|LineConditionInterface|ConditionalLineBreak $tokens
     * @return void
     */
    public function setTokens(array $tokens)
    {
        // reset the internally stored values
        unset($this->tokens);
        unset($this->lineBreakTokens);
        $this->tokens = array();
        $this->lineBreakTokens = array();
        // save the new tokens
        $this->add($tokens);
    }

    /**
     * This method returns the representation of the line for
     *
     * @param int $level Indicator for the level you want the lines split at
     * @return array
     */
    public function splitLine($level)
    {
        // reset the index information for the line breaks
        $this->resetLineBreakIndex();
        // get the array of arrays containing the compiled tokens
        return $this->getCurrentLines($level);
    }

    /**
     * @param int $sortOrder
     * @param array &$lineBreakData
     * @return array
     */
    public function splitLineBySortOrder($sortOrder, &$lineBreakData)
    {
        // reset the index information for the line breaks
        $this->resetLineBreakIndex();
        // get the array of arrays containing the compiled tokens
        return $this->getCurrentLinesBySortOrder($sortOrder, $lineBreakData);
    }

    /**
     * This method resets the index values for the line breaks.
     *
     * @return void
     */
    protected function resetLineBreakIndex()
    {
        // reset the index information for the line breaks
        $lineBreakToken = reset($this->lineBreakTokens);
        while ($lineBreakToken) {
            // reset the index directly in the item in memory
            $this->lineBreakTokens[key($this->lineBreakTokens)][self::ATTRIBUTE_INDEX] = 0;
            $lineBreakToken = next($this->lineBreakTokens);
        }
    }

    /**
     * This method saves information about a newly added token that happens to be a line break.
     *
     * @param LineBreak $lineBreak Token being placed in the string.
     * @return void
     */
    protected function saveLineBreakToken(LineBreak $lineBreak)
    {
        // determine how the line break information is going to be saved (called the id)
        $lineBreakId = $lineBreak->getGroupingId();
        // if the key doesn't exist in the array, then add an array so the next part will work
        if (!array_key_exists($lineBreakId, $this->lineBreakTokens)) {
            $this->lineBreakTokens[$lineBreakId] = array();
        }
        // increment the total count
        if (!array_key_exists(self::ATTRIBUTE_TOTAL, $this->lineBreakTokens[$lineBreakId])) {
            $this->lineBreakTokens[$lineBreakId][self::ATTRIBUTE_TOTAL] = 0;
            $this->lineBreakTokens[$lineBreakId][self::ATTRIBUTE_INDEX] = 0;
            $this->lineBreakTokens[$lineBreakId][self::ATTRIBUTE_SORT_ORDER] = $lineBreak->getSortOrder();
        }
        $this->lineBreakTokens[$lineBreakId][self::ATTRIBUTE_TOTAL]++;
    }

    /**
     * @param int $level
     * @return array
     */
    private function getCurrentLines($level)
    {
        $currentLines = array();
        $index = 0;
        $lineBreakData = array();
        // build up the string by compiling the tokens
        foreach ($this->tokens as $token) {
            // if no current line, create one and put it in the array
            if ($index >= sizeof($currentLines)) {
                $currentLines[$index] = new Line();
            }
            if ($token instanceof HardLineBreak) {
                $currentLines[$index]->add($token);
                $index++;
            } elseif ($token instanceof LineBreak) {
                $groupingId = $token->getGroupingId();
                $resolvedToken = $token->getValue(
                    $level,
                    $this->lineBreakTokens[$groupingId][self::ATTRIBUTE_INDEX]++,
                    $this->lineBreakTokens[$groupingId][self::ATTRIBUTE_TOTAL],
                    $lineBreakData
                );
                // if the token was resolved, then add the resolved token into the line
                if (false !== $resolvedToken) {
                    $currentLines[$index]->add($resolvedToken);
                    if ($resolvedToken instanceof HardLineBreak) {
                        $index++;
                    }
                } else {
                    // otherwise, just add the token back into the string
                    $currentLines[$index]->add($token);
                }
            } else {
                // add the current token to the end of the current line
                $currentLines[$index]->add($token);
            }
        }
        return $currentLines;
    }

    /**
     * This method breaks the current line into additional lines, based on the sort order.
     *
     * @param int $sortOrder
     * @param array &$lineBreakData
     * @return array
     */
    private function getCurrentLinesBySortOrder($sortOrder, &$lineBreakData)
    {
        $currentLines = array();
        $index = 0;
        // break down the line by only resolving the line breaks based on sort order
        foreach ($this->tokens as $token) {
            // if no current line, create one and put it in the array
            if (!array_key_exists($index, $currentLines)) {
                $currentLines[$index] = new Line();
            }
            if ($token instanceof ConditionalLineBreak) {
                $groupingId = $token->getGroupingId();
                // resolve the token if the conditional should be resolved
                if ($this->lineBreakTokens[$groupingId][self::ATTRIBUTE_SORT_ORDER] <= $sortOrder) {
                    $resolvedToken = $token->getValue(
                        1,
                        $this->lineBreakTokens[$groupingId][self::ATTRIBUTE_INDEX]++,
                        $this->lineBreakTokens[$groupingId][self::ATTRIBUTE_TOTAL],
                        $lineBreakData
                    );
                    // if the token was actually resolved, use that instead of the original
                    if (false !== $resolvedToken) {
                        $token = $resolvedToken;
                    }
                }
            }
            // add the current token to the end of the current line
            $currentLines[$index]->add($token);
            // if the token represents a new line, then create one
            if ($token instanceof HardLineBreak) {
                $index++;
            }
        }
        return $currentLines;
    }
}
