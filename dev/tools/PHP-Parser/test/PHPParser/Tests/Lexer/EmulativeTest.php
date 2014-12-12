<?php

class PHPParser_Tests_Lexer_EmulativeTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPParser_Lexer_Emulative */
    protected $lexer;

    protected function setUp()
    {
        $this->lexer = new PHPParser_Lexer_Emulative();
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testReplaceKeywords($keyword, $expectedToken)
    {
        $this->lexer->startLexing('<?php ' . $keyword);

        $this->assertEquals($expectedToken, $this->lexer->getNextToken());
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testNoReplaceKeywordsAfterObjectOperator($keyword)
    {
        $this->lexer->startLexing('<?php ->' . $keyword);

        $this->assertEquals(PHPParser_Parser::T_OBJECT_OPERATOR, $this->lexer->getNextToken());
        $this->assertEquals(PHPParser_Parser::T_STRING, $this->lexer->getNextToken());
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    public function provideTestReplaceKeywords()
    {
        return [
            // PHP 5.5
            ['finally',       PHPParser_Parser::T_FINALLY],
            ['yield',         PHPParser_Parser::T_YIELD],

            // PHP 5.4
            ['callable',      PHPParser_Parser::T_CALLABLE],
            ['insteadof',     PHPParser_Parser::T_INSTEADOF],
            ['trait',         PHPParser_Parser::T_TRAIT],
            ['__TRAIT__',     PHPParser_Parser::T_TRAIT_C],

            // PHP 5.3
            ['__DIR__',       PHPParser_Parser::T_DIR],
            ['goto',          PHPParser_Parser::T_GOTO],
            ['namespace',     PHPParser_Parser::T_NAMESPACE],
            ['__NAMESPACE__', PHPParser_Parser::T_NS_C],
        ];
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLexNewFeatures($code, array $expectedTokens)
    {
        $this->lexer->startLexing('<?php ' . $code);

        foreach ($expectedTokens as $expectedToken) {
            list($expectedTokenType, $expectedTokenText) = $expectedToken;
            $this->assertEquals($expectedTokenType, $this->lexer->getNextToken($text));
            $this->assertEquals($expectedTokenText, $text);
        }
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLeaveStuffAloneInStrings($code)
    {
        $stringifiedToken = '"' . addcslashes($code, '"\\') . '"';
        $this->lexer->startLexing('<?php ' . $stringifiedToken);

        $this->assertEquals(PHPParser_Parser::T_CONSTANT_ENCAPSED_STRING, $this->lexer->getNextToken($text));
        $this->assertEquals($stringifiedToken, $text);
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    public function provideTestLexNewFeatures()
    {
        return [
            ['0b1010110', [
                [PHPParser_Parser::T_LNUMBER, '0b1010110'],
            ]],
            ['0b1011010101001010110101010010101011010101010101101011001110111100', [
                [PHPParser_Parser::T_DNUMBER, '0b1011010101001010110101010010101011010101010101101011001110111100'],
            ]],
            ['\\', [
                [PHPParser_Parser::T_NS_SEPARATOR, '\\'],
            ]],
            ["<<<'NOWDOC'\nNOWDOC;\n", [
                [PHPParser_Parser::T_START_HEREDOC, "<<<'NOWDOC'\n"],
                [PHPParser_Parser::T_END_HEREDOC, 'NOWDOC'],
                [ord(';'), ';'],
            ]],
            ["<<<'NOWDOC'\nFoobar\nNOWDOC;\n", [
                [PHPParser_Parser::T_START_HEREDOC, "<<<'NOWDOC'\n"],
                [PHPParser_Parser::T_ENCAPSED_AND_WHITESPACE, "Foobar\n"],
                [PHPParser_Parser::T_END_HEREDOC, 'NOWDOC'],
                [ord(';'), ';'],
            ]],
        ];
    }
}
