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
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);
        // if a string or number is encountered, save off the original so that it can be used in the generated code.
        if (PHPParser_Parser::T_CONSTANT_ENCAPSED_STRING === $tokenId || PHPParser_Parser::T_ENCAPSED_AND_WHITESPACE === $tokenId || PHPParser_Parser::T_LNUMBER === $tokenId || PHPParser_Parser::T_DNUMBER === $tokenId) {
            $endAttributes[self::ORIGINAL_VALUE] = $value;
        } elseif ($tokenId == PHPParser_Parser::T_END_HEREDOC) {
            // only need to save the close tag and recreate the open take
            // because the parser saves the text with the closing element
            $endAttributes[self::HEREDOC_CLOSE_TAG] = $value;
        }
        return $tokenId;
    }
}
