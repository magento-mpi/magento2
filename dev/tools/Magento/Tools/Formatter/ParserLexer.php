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
                $startAttributes['startLine'] = $this->line;
                $endAttributes['endLine']     = $this->line;

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
                    $startAttributes['comments'][] = new \PHPParser_Comment($token[1], $token[2]);
                } elseif (T_DOC_COMMENT === $token[0]) {
                    $startAttributes['comments'][] = new \PHPParser_Comment_Doc($token[1], $token[2]);
                } elseif ($newlineCount > 1) {
                    // We found more than one newline, could be developer added spacing pretend it's a comment to preserve it.
                    for ($i = 1; $i < $newlineCount; $i++) {
                        $startAttributes['comments'][] = new \PHPParser_Comment_Doc("\n", $token[2]);
                    }
                } elseif (!isset($this->dropTokens[$token[0]])) {
                    $value = $token[1];
                    $startAttributes['startLine'] = $token[2];
                    $endAttributes['endLine']     = $this->line;

                    $tokenId = $this->tokenMap[$token[0]];
                    break;
                }
            }
        }

        $startAttributes['startLine'] = $this->line;

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
}
