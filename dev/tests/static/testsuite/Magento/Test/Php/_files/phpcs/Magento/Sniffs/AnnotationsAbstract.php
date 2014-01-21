<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Base of the annotations sniffs
 *
 * @SuppressWarnings(PHPMD)
 */
abstract class Magento_Sniffs_AnnotationsAbstract implements PHP_CodeSniffer_Sniff
{
    const AMBIGUOUS_TYPE = 'AmbiguousType';
    const MISSING = 'Missing';
    const WRONG_STYLE = 'WrongStyle';
    const WRONG_END = 'WrongEnd';
    const FAILED_PARSE = 'FailedParse';
    const CONTENT_AFTER_OPEN = 'ContentAfterOpen';
    const MISSING_SHORT = 'MissingShort';
    const NO_DOC = 'Empty';
    const SPACING_BEFORE_SHORT = 'SpacingBeforeShort';
    const SPACING_BEFORE_TAGS = 'SpacingBeforeTags';
    const SHORT_SINGLE_LINE = 'ShortSingleLine';
    const SHORT_NOT_CAPITAL = 'ShortNotCapital';
    const SPACING_AFTER = 'SpacingAfter';
    const SEE_ORDER = 'SeeOrder';
    const EMPTY_SEE = 'EmptySee';
    const DUPLICATE_RETURN = 'DuplicateReturn';
    const MISSING_PARAM_TAG = 'MissingParamTag';
    const SPACING_AFTER_LONG_NAME = 'SpacingAfterLongName';
    const SPACING_AFTER_LONG_TYPE = 'SpacingAfterLongType';
    const MISSING_PARAM_TYPE = 'MissingParamType';
    const MISSING_PARAM_NAME = 'MissingParamName';
    const EXTRA_PARAM_COMMENT = 'ExtraParamComment';
    const PARAM_NAME_NO_MATCH = 'ParamNameNoMatch';
    const PARAM_NAME_NO_CASE_MATCH = 'ParamNameNoCaseMatch';
    const INVALID_TYPE_HINT = 'InvalidTypeHint';
    const INCORRECT_TYPE_HINT = 'IncorrectTypeHint';
    const TYPE_HINT_MISSING = 'TypeHintMissing';
    const INCORRECT_PARAM_VAR_NAME = 'IncorrectParamVarName';
    const RETURN_ORDER = 'ReturnOrder';
    const MISSING_RETURN_TYPE = 'MissingReturnType';
    const INVALID_RETURN = 'InvalidReturn';
    const INVALID_RETURN_VOID = 'InvalidReturnVoid';
    const INVALID_NO_RETURN = 'InvalidNoReturn';
    const INVALID_RETURN_NOT_VOID = 'InvalidReturnNotVoid';
    const INCORRECT_INHERIT_DOC = 'IncorrectInheritDoc';
    const RETURN_INDENT = 'ReturnIndent';
    const MISSING_RETURN = 'MissingReturn';
    const RETURN_NOT_REQUIRED = 'ReturnNotRequired';
    const INVALID_THROWS = 'InvalidThrows';
    const THROWS_NOT_CAPITAL = 'ThrowsNotCapital';
    const THROWS_ORDER = 'ThrowsOrder';
    const SPACING_AFTER_PARAMS = 'SpacingAfterParams';
    const SPACING_BEFORE_PARAMS = 'SpacingBeforeParams';
    const SPACING_BEFORE_PARAM_TYPE = 'SpacingBeforeParamType';

    const ERROR = 0; // tells phpcs to use the default level
    const WARNING = 6; // default level of warnings is 5
    const INFO = 1;
    protected static $reportingLevel = array(
        self::AMBIGUOUS_TYPE => self::WARNING,
        self::MISSING => self::WARNING,
        self::WRONG_STYLE => self::WARNING,
        self::WRONG_END => self::WARNING,
        self::FAILED_PARSE => self::WARNING,
        self::NO_DOC    => self::WARNING,
        self::CONTENT_AFTER_OPEN    => self::WARNING,
        self::MISSING_SHORT => self::WARNING,
        self::NO_DOC => self::WARNING,
        self::SPACING_BEFORE_SHORT => self::WARNING,
        self::SPACING_BEFORE_TAGS => self::WARNING,
        self::SHORT_SINGLE_LINE => self::WARNING,
        self::SHORT_NOT_CAPITAL => self::WARNING,
        self::SPACING_AFTER => self::WARNING,
        self::SEE_ORDER => self::WARNING,
        self::EMPTY_SEE => self::WARNING,
        self::DUPLICATE_RETURN => self::WARNING,
        self::MISSING_PARAM_TAG => self::WARNING,
        self::SPACING_AFTER_LONG_NAME => self::WARNING,
        self::SPACING_AFTER_LONG_TYPE => self::WARNING,
        self::MISSING_PARAM_TYPE => self::WARNING,
        self::MISSING_PARAM_NAME => self::WARNING,
        self::EXTRA_PARAM_COMMENT => self::WARNING,
        self::PARAM_NAME_NO_MATCH => self::WARNING,
        self::PARAM_NAME_NO_CASE_MATCH => self::WARNING,
        self::INVALID_TYPE_HINT => self::WARNING,
        self::INCORRECT_TYPE_HINT => self::WARNING,
        self::TYPE_HINT_MISSING => self::WARNING,
        self::INCORRECT_PARAM_VAR_NAME => self::WARNING,
        self::RETURN_ORDER => self::WARNING,
        self::MISSING_RETURN_TYPE => self::WARNING,
        self::INVALID_RETURN => self::WARNING,
        self::INVALID_RETURN_VOID => self::WARNING,
        self::INVALID_NO_RETURN => self::WARNING,
        self::INVALID_RETURN_NOT_VOID => self::WARNING,
        self::INCORRECT_INHERIT_DOC => self::WARNING,
        self::RETURN_INDENT => self::WARNING,
        self::MISSING_RETURN => self::WARNING,
        self::RETURN_NOT_REQUIRED => self::WARNING,
        self::INVALID_THROWS => self::WARNING,
        self::THROWS_NOT_CAPITAL => self::WARNING,
        self::THROWS_ORDER => self::WARNING,
        self::SPACING_AFTER_PARAMS => self::WARNING,
        self::SPACING_BEFORE_PARAMS => self::WARNING,
        self::SPACING_BEFORE_PARAM_TYPE => self::WARNING,
    );
    /** 
     * List of allowed types
     *
     * @var string[]
     */
    protected static $allowedTypes = array(
                                   'array',
                                   'boolean',
                                   'bool',
                                   'float',
                                   'integer',
                                   'int',
                                   'object',
                                   'string',
                                   'resource',
                                   'callable',
                                  );
    /**
     * This method will add the message as an error or warning depending on the configuration
     *
     * @param string $error    The error message.
     * @param int    $stackPtr The stack position where the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the error message.
     * @param int    $severity The severity level for this error. A value of 0
     */
    protected function addMessage($message, $stackPtr, $code, $data = array(), $severity = 0)
    {
        //
        // Does the $code key exist in the report level
        if (array_key_exists($code, self::$reportingLevel)) {
            $level = self::$reportingLevel[$code];
            if ($level === self::WARNING || $level === self::INFO) {
                $s = $level;
                if ($severity !== 0) {
                    $s = $severity;
                }
                $this->currentFile->addWarning($message, $stackPtr, $code, $data, $s);
            } else {
                $this->currentFile->addError($message, $stackPtr, $code, $data, $severity);
            }
        }
    }

    /**
     * Determine if text is a class name
     *
     * @param string $class
     * @return bool
     */
    protected function isClassName($class)
    {
        $return = false;
        if (preg_match('/^\\\\?[A-Z]\\w+(?:\\\\\\w+)*?$/', $class)) {
            $return = true;
        }
    }
    /**
     * Take the type and suggest the correct one.
     *
     * @param string $type
     * @return string
     */
    protected function suggestType($type)
    {
        $suggestedName = null;
        // First check to see if this type is a list of types. If so we break it up and check each
        if (preg_match('/^.*?(?:\|.*)+$/', $type)) {
            // Return list of all types in this string.
            $types = explode('|', $type);
            if (is_array($types)) {
                // Loop over all types and call this method on each.
                $suggestions = array();
                foreach ($types as $t) {
                    $suggestions[] = $this->suggestType($t);
                }
                // Now that we have suggestions put them back together.
                $suggestedName = implode('|', $suggestions);
            } else {
                $suggestedName = 'Unknown';
            }
        } elseif ($this->isClassName($type)) {
            // If this looks like a class name.
            $suggestedName = $type;
        } else {
            // Only one type First check if that type is a base one.
            $lowerVarType = strtolower($type);
            switch ($lowerVarType) {
                case 'boolean':
                    $suggestedName = 'bool';
                    break;
                case 'integer':
                    $suggestedName = 'int';
                    break;
            }//end switch
            // If no name suggested yet then call the phpcs version of this method.
            if (empty($suggestedName)) {
                $suggestedName = PHP_CodeSniffer::suggestType($type);
            }
        }
        return $suggestedName;
    }
}
