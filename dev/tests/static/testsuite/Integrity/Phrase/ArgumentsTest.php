<?php
/**
 * Scan source code for detects invocations of __() function, analyzes placeholders with arguments
 * and see if they not equal
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @copyright   {copyright}
 * @license     {license_link}
 */
class ArgumentsTest extends PHPUnit_Framework_TestCase
{
    const FILES_MASK = '/\.(php|phtml)$/';

    /**
     * @var Tokenizer
     */
    private $_tokenizer;

    protected function setUp()
    {
        $this->_tokenizer = new Tokenizer();
    }

    public function testPhraseArguments()
    {
        $errors = array();
        foreach ($this->_getFiles() as $file) {
            $this->_tokenizer->parse($file);
            foreach ($this->_tokenizer->getPhrases() as $phrase) {
                if (!is_null($phrase['phrase'])
                    && (preg_match_all('/%(\d+)/', $phrase['phrase'], $matches) || $phrase['arguments'] > 0)
                ) {
                    $placeholdersInPhrase = array_unique($matches[1]);
                    if (count($placeholdersInPhrase) != $phrase['arguments']) {
                        $errors[] = 'The number of arguments is not equal to the number of placeholders.' .
                            "\nPhrase: " . $phrase['phrase'] .
                            "\nFile: " . $phrase['file'] .
                            "\nLine: " . $phrase['line'];
                    }
                }
            }
        }
        $this->assertEmpty($errors, $this->_prepareErrorMessage($errors));
    }

    /**
     * Prepare error message
     *
     * @param array $errors
     * @return string
     */
    protected function _prepareErrorMessage($errors)
    {
        $errorMessage = "\n" . implode("\n\n", $errors);
        return sprintf('We have found %s error(s): %s', count($errors), $errorMessage);
    }

    /**
     * Get files for scan
     *
     * @return array
     */
    protected function _getFiles()
    {
        $path = Utility_Files::init()->getPathToSource() . '/app/';
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        return new RegexIterator($files, self::FILES_MASK);
    }
}

/**
 * Class TokenException
 */
class TokenException extends Exception
{
}

/**
 * Class Token
 */
class Token
{
    /**
     * @var int|string
     */
    protected $_value;

    /**
     * @var int|string
     */
    protected $_name = '';

    /**
     * @var int
     */
    protected $_line = 0;

    /**
     * Get line of token beginning
     *
     * @return int
     */
    public function getLine()
    {
        return $this->_line;
    }

    /**
     * Get token name
     *
     * @return int|string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get token value
     *
     * @return int|string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Build token
     *
     * @param array $tokenData
     */
    public function __construct($tokenData)
    {
        if (is_array($tokenData)) {
            $this->_name = $tokenData[0];
            $this->_value = $tokenData[1];
            $this->_line = $tokenData[2];
        } else {
            $this->_value = $this->_name = $tokenData;
        }
    }
}

/**
 * Class Tokenizer
 */
class Tokenizer
{
    /**
     * @var array
     */
    protected $_phrases = array();

    /**
     * @var array
     */
    protected $_tokens = array();

    /**
     * @var string
     */
    protected $_file;

    /**
     * @var int
     */
    private $_openBrackets;

    /**
     * @var int
     */
    private $_closeBrackets;

    /**
     * Get phrases array
     *
     * @return array
     */
    public function getPhrases()
    {
        return $this->_phrases;
    }

    /**
     * Parse given file
     *
     * @param string $file
     */
    public function parse($file)
    {
        $this->_phrases = array();
        $content = file_get_contents($file);
        $this->_file = $file;
        $this->_tokens = token_get_all($content);
        try {
            for (; ;) {
                $this->_findPhrases();
            }
        } catch (TokenException $pe) {
            // probably syntax error. do nothing
        }
    }

    /**
     * Find phrases into given token. e.g.: __('phrase', args)
     *
     * @throws TokenException
     */
    protected function _findPhrases()
    {
        $phraseStartToken = $this->_getNextToken();
        if ($this->_tokenIsPhraseFunction($phraseStartToken) && $this->_getNextToken()->getValue() == '(') {
            $arguments = $this->_getArgumentsTokens();
            $phrase = $this->_collectPhrase(array_shift($arguments));
            $this->_phrases[] = array(
                'phrase' => $phrase,
                'arguments' => count($arguments),
                'file' => $this->_file,
                'line' => $phraseStartToken->getLine(),
            );
        }
    }

    /**
     * Whenever token is phrase function
     *
     * @param Token $token
     * @return bool
     */
    protected function _tokenIsPhraseFunction(Token $token)
    {
        return $token->getName() == T_STRING && $token->getValue() == '__';
    }

    /**
     * Get arguments tokens of function
     *
     * @return array
     */
    protected function _getArgumentsTokens()
    {
        $arguments = array();
        try {
            $this->_openBrackets = 1;
            $this->_closeBrackets = 0;
            $argumentN = 0;
            while (true) {
                $token = $this->_getNextToken();
                if ($token->getValue() == ';') {
                    break;
                }
                if ($token->getValue() == '(') {
                    $this->_skipInnerArgumentInvoke();
                    continue;
                }
                if ($token->getValue() == ')') {
                    $this->_closeBrackets++;
                }
                $arguments[$argumentN][] = $token;
                if ($token->getName() == ',' && $this->_isInnerArgumentClosed()) {
                    array_pop($arguments[$argumentN]);
                    $argumentN++;
                }
                if ($this->_openBrackets == $this->_closeBrackets) {
                    break;
                }
            }
        } catch (Exception $e) {
            return array();
        }
        return $arguments;
    }

    /**
     * Whenever inner argument closed
     *
     * @return bool
     */
    private function _isInnerArgumentClosed()
    {
        return ($this->_openBrackets - 1) == $this->_closeBrackets;
    }

    /**
     * Skip invoke the inner argument of __()
     */
    private function _skipInnerArgumentInvoke()
    {
        $this->_openBrackets++;
        while ($this->_getNextToken()->getValue() != ')') {
            if ($this->_getCurrentToken()->getValue() == ')') {
                $this->_closeBrackets++;
            }
            if ($this->_getCurrentToken()->getValue() == '(') {
                $this->_openBrackets++;
            }
        }
        $this->_closeBrackets++;
    }

    /**
     * Collect all phrase parts into string. Return null if phrase is a variable
     *
     * @param array  $phraseTokens
     * @return string|null
     */
    public function _collectPhrase($phraseTokens)
    {
        $phrase = array();
        if ($phraseTokens) {
            $isNotLiteral = true;
            /** @var $phraseToken Token */
            foreach ($phraseTokens as $phraseToken) {
                if ($phraseToken->getName() == T_CONSTANT_ENCAPSED_STRING) {
                    $phrase[] = $this->_clearPhrase($phraseToken->getValue());
                    $isNotLiteral = false;
                }
            }
            if ($isNotLiteral) {
                return null;
            }
        }
        return implode(' ', $phrase);
    }

    /**
     * Clear phrase
     *
     * @param string $phrase
     * @return string
     */
    protected function _clearPhrase($phrase)
    {
        return trim($phrase, "'\"");
    }

    /**
     * Build token from array
     *
     * @param array $tokenData
     * @return Token
     */
    private function _buildToken($tokenData)
    {
        return new Token($tokenData);
    }

    /**
     * Get Next Token
     *
     * @return Token
     * @throws TokenException
     */
    protected function _getNextToken()
    {
        $token = next($this->_tokens);
        if ($token) {
            return $this->_buildToken($token);
        }
        throw new TokenException('Next token does not exist');
    }

    /**
     * Get current token
     *
     * @return Token
     * @throws TokenException
     */
    protected function _getCurrentToken()
    {
        $token = current($this->_tokens);
        if ($token) {
            return $this->_buildToken($token);
        }
        throw new TokenException('Token does not exist');
    }
}
