<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter;

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
     * This method retrieves the next available token. Original values are stored for strings and numbers so that they
     * can be used in the pretty printer.
     * @param null $value
     * @param null $startAttributes
     * @param null $endAttributes
     * @return int
     */
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null)
    {
        $startAttributes = array();
        $endAttributes   = array();

        // 0 is the EOF token
        $tokenId = 0;

        while (isset($this->tokens[++$this->pos])) {
            $token = $this->tokens[$this->pos];

            if (is_string($token)) {
                $startAttributes[self::START_LINE_KEY] = $this->line;
                $endAttributes[self::END_LINE_KEY]     = $this->line;

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
                $newlineCount = substr_count($token[1], "\n");
                $this->line += $newlineCount;

                if (T_COMMENT === $token[0]) {
                    $startAttributes[self::COMMENT_KEY][] = new \PHPParser_Comment($token[1], $token[2]);
                } elseif (T_DOC_COMMENT === $token[0]) {
                    $startAttributes[self::COMMENT_KEY][] = new \PHPParser_Comment_Doc($token[1], $token[2]);
                } elseif (!isset($this->dropTokens[$token[0]])) {
                    $value = $token[1];
                    $startAttributes[self::START_LINE_KEY] = $token[2];
                    $endAttributes[self::END_LINE_KEY]     = $this->line;

                    $tokenId = $this->tokenMap[$token[0]];
                    break;
                } elseif (array_key_exists(self::COMMENT_KEY, $startAttributes) || $newlineCount > 1) {
                    $this->handleBlankLines($newlineCount, $token, $startAttributes);
                }
            }
        }

        $startAttributes[self::START_LINE_KEY] = $this->line;

        // if a string or number is encountered, save off the original so that it can be used in the generated code.
        if (PHPParser_Parser::T_CONSTANT_ENCAPSED_STRING === $tokenId ||
            PHPParser_Parser::T_ENCAPSED_AND_WHITESPACE === $tokenId ||
            PHPParser_Parser::T_LNUMBER === $tokenId || PHPParser_Parser::T_DNUMBER === $tokenId) {
            $endAttributes[self::ORIGINAL_VALUE] = $value;
        } elseif ($tokenId == PHPParser_Parser::T_END_HEREDOC) {
            // only need to save the close tag and recreate the open take
            // because the parser saves the text with the closing element
            $endAttributes[self::HEREDOC_CLOSE_TAG] = $value;
        }

        return $tokenId;
    }

    private function handleBlankLines($newlineCount, $token, &$startAttributes)
    {
        // This line is not a comment or code
        // This line has preceding comments or it is more than one newline in it
        // Thus we check to see if we have comments
        if (array_key_exists(self::COMMENT_KEY, $startAttributes)) {
            // If we have comments then see if the last one has a newline at the end
            $attId = sizeof($startAttributes[self::COMMENT_KEY]);
            if ($attId > 0 && preg_match('/.*\n$/', $startAttributes[self::COMMENT_KEY][$attId-1])) {
                // if so then count it as part of this since it could be a blank line after a comment
                $newlineCount++;
            }
        }
        // If newline count is more than one the it could be developer added spacing
        if ($newlineCount > 1) {
            // We found more than one newline, pretend it's a comment to preserve it.
            for ($i = 1; $i < $newlineCount; $i++) {
                $startAttributes[self::COMMENT_KEY][] = new \PHPParser_Comment_Doc("\n", $token[2]);
            }
        }
    }
}
