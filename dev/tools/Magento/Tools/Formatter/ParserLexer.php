<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use PHPParser_Comment;
use PHPParser_Comment_Doc;
use PHPParser_Lexer;
use PHPParser_Parser;

/**
 * This class is used to parse the code as input.
 */
class ParserLexer extends PHPParser_Lexer
{
    /**
     * This constant is used to tag the original value in certain string cases.
     */
    const ORIGINAL_VALUE = 'originalValue';

    /**
     * This constant is used to tag the heredoc value.
     */
    const HEREDOC_CLOSE_TAG = 'heredocCloseTag';

    /**
     * This constant is used to tag the heredoc value.
     */
    const IS_NOWDOC = 'isNowDoc';

    /**
     * Constant for comment key
     */
    const COMMENT_KEY = 'comments';

    /**
     * Constant for startLine key
     */
    const START_LINE_KEY = 'startLine';

    /**
     * Constant for endLine key
     */
    const END_LINE_KEY = 'endLine';

    /**
     * Map of comments indexed by the line number containing the comment
     *
     * @var array
     */
    public $commentMap;

    /**
     * This member holds the culmination of the current heredoc
     * @var string $heredocValue
     */
    protected $heredocValue;

    /**
     * This member holds an array of tokens that should just capture the original value.
     *
     * @var array $simpleOriginalValueTokens
     */
    protected $simpleOriginalValueTokens = [
        PHPParser_Parser::T_CONSTANT_ENCAPSED_STRING,
        PHPParser_Parser::T_ENCAPSED_AND_WHITESPACE,
        PHPParser_Parser::T_LNUMBER,
        PHPParser_Parser::T_DNUMBER,
    ];

    /**
     * This method returns the comment map.
     *
     * @return array
     */
    public function getCommentMap()
    {
        return $this->commentMap;
    }

    /**
     * This method retrieves the next available token. Original values are stored for strings and numbers so that they
     * can be used in the pretty printer.
     *
     * @param string|null &$value
     * @param array|null &$startAttributes
     * @param array|null &$endAttributes
     * @return int
     */
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null)
    {
        // Initialize the attribute arrays
        $startAttributes = [];
        $endAttributes = [];
        // 0 is the EOF token
        $tokenId = 0;
        // Loop over tokens to process them
        while (isset($this->tokens[++$this->pos])) {
            $token = $this->tokens[$this->pos];
            if (is_string($token)) {
                $startAttributes[self::START_LINE_KEY] = $this->line;
                $endAttributes[self::END_LINE_KEY] = $this->line;

                // bug in token_get_all
                if ('b"' === $token) {
                    $value = 'b"';
                    $tokenId = ord('"');
                    break;
                } else {
                    $value = $token;
                    $tokenId = ord($token);
                    break;
                }
            } else {
                $newlineCount = substr_count($token[1], HardLineBreak::EOL);
                $this->line += $newlineCount;

                if (T_COMMENT === $token[0]) {
                    $startAttributes[self::COMMENT_KEY][] = new PHPParser_Comment($token[1], $token[2]);
                    $this->commentMap[$token[2]] = $token[1];
                } elseif (T_DOC_COMMENT === $token[0]) {
                    $startAttributes[self::COMMENT_KEY][] = new PHPParser_Comment_Doc($token[1], $token[2]);
                    $this->commentMap[$token[2]] = $token[1];
                } elseif (!isset($this->dropTokens[$token[0]])) {
                    $value = $token[1];
                    $startAttributes[self::START_LINE_KEY] = $token[2];
                    $endAttributes[self::END_LINE_KEY] = $this->line;

                    $tokenId = $this->tokenMap[$token[0]];
                    break;
                } else {
                    $this->handleBlankLines($newlineCount, $token, $startAttributes);
                }
            }
        }
        // Add line to start attributes
        $startAttributes[self::START_LINE_KEY] = $this->line;
        // Handle Strings so that original values are preserved
        $this->handleStrings($tokenId, $value, $startAttributes, $endAttributes);
        // Return Token Id
        return $tokenId;
    }

    /**
     * This method takes tokenId, value, and endAttributes reference and then does substitution to perserve the original
     * number or strings that were in the code.
     *
     * @param int $tokenId
     * @param string $value
     * @param array &$startAttributes
     * @param array &$endAttributes
     * @return void
     */
    private function handleStrings($tokenId, $value, &$startAttributes, &$endAttributes)
    {
        // if a string or number is encountered, save off the original so that it can be used in the generated code.
        if (in_array($tokenId, $this->simpleOriginalValueTokens)) {
            $endAttributes[self::ORIGINAL_VALUE] = $value;
        } elseif ($tokenId == PHPParser_Parser::T_START_HEREDOC) {
            if (preg_match('/<<<\'.*?\'/', $value)) {
                $startAttributes[self::IS_NOWDOC] = true;
            }
            // flag that the context of being a heredoc value
            $this->heredocValue = '';
        } elseif ($tokenId == PHPParser_Parser::T_END_HEREDOC) {
            // only need to save the close tag and recreate the open take
            // because the parser saves the text with the closing element
            $endAttributes[self::HEREDOC_CLOSE_TAG] = $value;
            $endAttributes[self::ORIGINAL_VALUE] = $this->heredocValue;
            // no longer in a heredoc, so stop recording
            $this->heredocValue = null;
        }
        // if in the context of a heredoc, just save off all the tokens
        if (null !== $this->heredocValue && $tokenId !== PHPParser_Parser::T_START_HEREDOC) {
            $this->heredocValue .= $value;
        }
    }

    /**
     * This method takes newlineCount, token, and startAttributes reference and adds any blank lines that are likely
     * developer added spacing to the list of startAttributes.
     *
     * @param int $newlineCount
     * @param array $token
     * @param array &$startAttributes
     * @return void
     */
    private function handleBlankLines($newlineCount, $token, &$startAttributes)
    {
        // This line is not a comment or code
        // This line has preceding comments or it is more than one newline in it
        // Thus we check to see if we have comments
        if (array_key_exists(self::COMMENT_KEY, $startAttributes)) {
            // If we have comments then see if the last one has a newline at the end
            $attId = sizeof($startAttributes[self::COMMENT_KEY]);
            if ($attId > 0 && preg_match('/.*\n$/', $startAttributes[self::COMMENT_KEY][$attId - 1])) {
                // if so then count it as part of this since it could be a blank line after a comment
                $newlineCount++;
            }
        }
        // If newline count is more than one the it could be developer added spacing
        if ($newlineCount > 1) {
            // We found more than one newline, pretend it's a comment to preserve it.
            for ($i = 1; $i < $newlineCount; $i++) {
                $startAttributes[self::COMMENT_KEY][] = new PHPParser_Comment_Doc("\n", $token[2]);
            }
        }
    }
}
