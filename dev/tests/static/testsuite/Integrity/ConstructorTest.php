<?php
use \Zend\Code\Reflection\MethodReflection;

/**
 * Check that signature of child class constructor is valid according to parent class constructor
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Integrity_ConstructorTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Integrity_ConstructorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Flag that defines how code style problems should be solved.
     *
     * This flag makes code style problems with methods and methods params parsing reported
     */
    const IGNORE_CODE_STYLE_PROBLEMS = true;

    /**
     * Pattern for nicely styled code
     *
     * Pattern only works under assumptions that:
             - PHPDoc starts from a new line
             - There are 4 space chars before PHPDoc
             - Method definition starts from a new line
             - There are 4 space chars before method keywords
             - Keywords (public, protected, private, static, function), method name are divided with one space
             - No space between method name and opening parenthesis
             - Method opening brace is placed on new line after 4 space chars OR like ") {"

             - Pattern for method with body (if closing bracket is placed correctly)

     * Notes:
             - PHPDoc can be omitted
             - visibility keyword is not required
             - 'static' keyword can go before or after visibility keyword

     * Matches:
             0*  => all
             1   => PHPDoc
             2   => 'static' keyword if exists
             3   => visibility keyword if exists
             4   => 'static' keyword if exists (case when 'static' used after visibility keyword)
             5   => method name
             6   => <methodParams> = [<paramDefinition>] [<divider> <paramDefinition>] ...'
                    where
                        <divider> = , {<space>|<lineBreak><indent>}
                        <paramDefinition> = [<paramType>] <paramName> [= <paramValue>]
             7   => method body
     */
    //@codingStandardsIgnoreStart
    const PATTERN_METHOD = '~(?:^\s{4}(/\*\*[^/]+/)\s{1,2})?(?:^\s{4})(?:(static)\s)?(?:(public|protected|private)\s)?(?:(static)\s)?(?:function\s(%s))\(((?Us).*)\)\s+\{((?Us).*)^\s{4}\}~m';
    //@codingStandardsIgnoreEnd

    /**
     * Pattern that is only used too check is the given method exists
     *
     *  It even works with broken code style
     */
    //@codingStandardsIgnoreStart
    const PATTERN_METHOD_IGNORE_CS = '~(?:(static)\s+)?(?:(public|protected|private)\s+)?(?:(static)\s+)?(?:function\s+(%s))\(~m';
    //@codingStandardsIgnoreEnd

    /**
     * Pattern for method param
     *
     * Pattern only works under assumptions that:
        - Param type and param name are divided with one space
        - Param value and param name are divided with ' = '
        - Param name can not start with symbol other than letter
     * Notes:
        - Param name can contain digits
     *
     */
    const PATTERN_PARAM = '~(?:(array|(?:%s))\s)?(%s)(?:\s=\s(.*))?~';

    /**
     * Pattern for method param when no code style is kept
     */
    const PATTERN_PARAM_IGNORE_CS = '~(?:(array|(?:%s))\s+)?(%s)(?:\s*=\s*(.*))?~';

    //@codingStandardsIgnoreStart
    const PATTERN_CLASS_DEFINITION = '~^(?:(abstract)\s)?class\s(%s)(?:\s+extends\s(%s))?(?:(?:\s+implements\s(%s)))?~m';
    //@codingStandardsIgnoreEnd

    const PATTERN_NAMESPACE = '~^namespace\s(%s);~m';

    const PATTERN_PARENT_CONSTRUCTOR_INVOCATION = '~^\s{8}parent::__construct\(([$\w\d_,\s]*)\);~m';

    /**
     * Pattern for Magento class
     *
     * It can not start with lowercase letter, digit or underscore
     */
    const PATTERN_PART_MAGENTO_CLASS = '[A-Z][a-z\d][A-Za-z\d_]+';

    /**
     * Pattern for PHP namespace
     *
     * It can not start with lowercase letter, digit or underscore
     */
    const PATTERN_PART_MAGENTO_NAMESPACE = '[A-Z][a-z\d][A-Za-z\d\\\]+';

    /**
     *  Pattern for PHP variable
     */
    const PATTERN_PART_VARIABLE = '\$[\w][\w\d_]+';

    /**
     * Pattern for PHP namespace usage declaration
     */
    const PATTERN_NAMESPACE_USES = '~^use\s+([\w\d\\\,\s]+);~m';

    /**
     * List of already found classes to avoid checking them over and over again
     *
     * @var array
     */
    protected static $_existingClasses = array();

    /**
     * @param string $file
     * @throws Exception
     * @dataProvider phpFileClassDataProvider
     */
    public function testPhpFileClass($file)
    {
        $contents = file_get_contents($file);

        //check multiple classes in single file

        try {
            $data = $this->_getConstructorData($contents);

            $hasConstructor = $data !== array();
            if (!$hasConstructor) {
                return;
            }

            $parentData = $this->_getParentConstructorData($contents);
            if (!$parentData) {
                return;
            }
            $this->_assertSignatureMatch($parentData, $data);

            if ($data['body']) {
                $invocationData = $this->_getParentConstructorInvocationData($data['body']);
                if ($invocationData !== null) {
                    $this->_assertInvocationMatch($parentData, $invocationData);
                }
            }
        } catch (Exception $e) {
            $this->fail(sprintf('Detected problem in file "%s": %s', $file, $e->getMessage()));
        }
    }

    /**
     * @return array
     */
    public function phpFileClassDataProvider()
    {
        $files = Utility_Files::init()->getPhpFiles(true, false, false);
        return $files;
    }

    /**
     * Get constructor data
     *
     * @param string $content
     * @return array
     * @see _getMethodData
     */
    protected function _getConstructorData($content)
    {
        //@TODO If file contains no class, or class contains no constructor current method returns array()?

        $method = '__construct';    // Case when constructor name differs from '__construct' is not processed
        $data = $this->_getMethodData($method, $content);

        return $data;
    }

    /**
     * Get method data
     *
     * Resulting arrays contains following keys:
     *  - phpDoc : string
     *  - static : bool
     *  - visibility : string = public|protected|private
     *  - method : string
     *  - paramsString: string
     *  - body : string
     *  - keywords : array
     *  - params : array
     *
     * @param string $methodName
     * @param string $content
     * @return array
     * @throws Exception
     */
    protected function _getMethodData($methodName, $content)
    {
        $hasConstructor = false;
        if (!self::IGNORE_CODE_STYLE_PROBLEMS) {
            $pattern = sprintf(self::PATTERN_METHOD_IGNORE_CS, $methodName);
            $matches = null;
            preg_match($pattern, $content, $matches);
            $hasConstructor = !empty($matches);
        }

        $pattern = sprintf(self::PATTERN_METHOD, $methodName);
        $matches = null;
        preg_match($pattern, $content, $matches);

        $data = array();
        if (empty($matches)) {
            if ($hasConstructor && !self::IGNORE_CODE_STYLE_PROBLEMS) {
                throw new Exception(sprintf('Invalid code style. Impossible to parse constructor'));
            }
        } else {
            $indexPhpDoc       = 1;
            $indexStaticBefore = 2;
            $indexVisibility   = 3;
            $indexStaticAfter  = 4;
            $indexMethodName   = 5;
            $indexMethodParams = 6;
            $indexMethodBody   = 7;

            //$data['matches']      = $matches;
            $data['phpDoc']       = $matches[$indexPhpDoc];
            $data['static']       = $matches[$indexStaticBefore] || $matches[$indexStaticAfter];
            $data['visibility']   = $matches[$indexVisibility] ? $matches[$indexVisibility] : 'public';
            $data['method']       = $matches[$indexMethodName];
            $data['paramsString'] = $matches[$indexMethodParams];
            $data['body']         = $matches[$indexMethodBody];

            $data['keywords']   = array();
            if ($matches[$indexVisibility]) {
                $data['keywords'][] = $matches[$indexVisibility];
            }
            if ($data['static']) {
                $data['keywords'][] = 'static';
            }

            $data['params'] = $this->_parseParams($matches[$indexMethodParams]);
        }

        return $data;
    }

    /**
     * Parse params string into an array
     *
     * Checks for possible code style problems if flag self::IGNORE_CODE_STYLE_PROBLEMS is not set
     *
     * @param string $paramsString
     * @return array
     * @see _parseParamsWithRegex
     */
    protected function _parseParams($paramsString)
    {
        $pattern = sprintf(self::PATTERN_PARAM, self::PATTERN_PART_MAGENTO_CLASS, self::PATTERN_PART_VARIABLE);
        $params = $this->_parseParamsWithRegex($paramsString, $pattern);

        if (!self::IGNORE_CODE_STYLE_PROBLEMS) {
            $pattern = sprintf(self::PATTERN_PARAM_IGNORE_CS,
                self::PATTERN_PART_MAGENTO_CLASS, self::PATTERN_PART_VARIABLE
            );
            $paramsCSIndependent = $this->_parseParamsWithRegex($paramsString, $pattern);

            $this->assertEquals($params, $paramsCSIndependent, 'Code style problems with constructor params.');
        }

        return $params;
    }

    /**
     * Parse params string into an array using given regex
     *
     * @param string $paramsString
     * @param string $pattern
     * @return array
     * @throws Exception
     */
    protected function _parseParamsWithRegex($paramsString, $pattern)
    {
        $indexType  = 1;
        $indexParam = 2;
        $indexValue = 3;

        $params = array();
        if ($paramsString) {
            $array = preg_split('~,\s+~', trim($paramsString));
            foreach ($array as $item) {
                $matches = null;

                $result = preg_match($pattern, $item, $matches);
                if ($result === false || $result === 0) {
                    throw new Exception(sprintf('Invalid param to match "%s"', $item));
                }

                $param = array();
                $param['name'] = $matches[$indexParam];
                if ($matches[$indexType]) {
                    $param['type'] = $matches[$indexType];
                }
                if (isset($matches[$indexValue])) {
                    $param['value'] = $matches[$indexValue];
                }
                $param['is_optional'] = isset($matches[$indexValue]);
                $params[] = $param;
            }
        }

        return $params;
    }

    /**
     * Get parent class for the class declared in $content
     *
     * @param string $content
     * @return string|null
     * @throws Exception
     */
    protected function _getParentClass($content)
    {
        $data = $this->_getClassDefinitionData($content);
        $parent = $data['parent'];
        if ($parent === null) {
            return null;
        }
        
        $namespace = $this->_getNamespace($content);
        $namespaceUses = $this->_getNamespaceUses($content);
        if ($namespace === null && $namespaceUses === array()) {
            return $parent;
        }

        $message = sprintf(
            'Namespace declaration/usage detected. '
            . 'Impossible to automatically resolve file containing parent class "%s".',
            $parent
        );
        throw new Exception($message);
    }

    /**
     * Get class declared in $content
     *
     * @param string $content
     * @param bool $errorOnNamespace
     * @return null|string
     * @throws Exception
     */
    protected function _getClass($content, $errorOnNamespace = true)
    {
        $data = $this->_getClassDefinitionData($content);
        $class = $data['class'];
        if ($class === null) {
            return null;
        }

        $namespace = $this->_getNamespace($content);
        if ($namespace === null) {
            return $class;
        }

        if ($errorOnNamespace) {
            $message = sprintf(
                'Class "%s" is used with namespace "%s". Automatic resolving is not working.',
                $class, $namespace
            );

            throw new Exception($message);
        } else {
            return $namespace . '\\' .$class;
        }
    }

    /**
     * Get data from class declared in $content
     *
     * @param string $content
     * @return array
     * @throws Exception
     */
    protected function _getClassDefinitionData($content)
    {
        $data = array();
        $matches = null;
        $pattern = sprintf(self::PATTERN_CLASS_DEFINITION,
            self::PATTERN_PART_MAGENTO_CLASS,
            self::PATTERN_PART_MAGENTO_CLASS,
            self::PATTERN_PART_MAGENTO_CLASS
        );

        preg_match($pattern, $content, $matches);

        if (!$matches) {
            throw new Exception('Invalid class definition');
        }

        $indexAbstract = 1;
        $indexClass = 2;
        $indexParent = 3;
        $indexInterfaces = 4;

        $data['class'] = $matches[$indexClass];
        if (isset($matches[$indexParent])) {
            $data['parent'] = $matches[$indexParent];   //can be empty string ""
        } else {
            $data['parent'] = null;
        }
        $data['abstract'] = (bool)$matches[$indexAbstract];
        if (isset($matches[$indexInterfaces])) {
            $data['interfaces'] = $matches[$indexInterfaces];
        }

        return $data;
    }

    /**
     * Get namespace declared in $content
     *
     * @param string $content
     * @return null
     */
    protected function _getNamespace($content)
    {
        $matches = null;
        $pattern = sprintf(self::PATTERN_NAMESPACE, self::PATTERN_PART_MAGENTO_NAMESPACE);

        preg_match($pattern, $content, $matches);

        $indexNamespace = 1;
        if (isset($matches[$indexNamespace])) {
            $namespace = $matches[$indexNamespace];
        } else {
            $namespace = null;
        }

        return $namespace;
    }

    /**
     * Get namespaces used in $content
     *
     * @param string $content
     * @return array
     */
    protected function _getNamespaceUses($content)
    {
        $matches = null;
        $pattern = sprintf(self::PATTERN_NAMESPACE_USES, self::PATTERN_PART_MAGENTO_NAMESPACE);

        preg_match_all($pattern, $content, $matches);

        $uses = array();
        if (!empty($matches)) {
            $indexAlias = 1;
            foreach ($matches[1] as $namespaces) {
                $parts = preg_split('~,\s+~', $namespaces);
                foreach ($parts as $namespace) {
                    $array = preg_split('~\s+as\s+~', $namespace);

                    $use = array();
                    $use['namespace'] = $array[0];
                    if (isset($array[$indexAlias])) {
                        $use['alias'] = $array[$indexAlias];
                    }

                    $uses[] = $use;
                }
            }
        }

        return $uses;
    }

    /**
     * Find file where given class is declared
     *
     * @param string $class
     * @return string|null
     */
    protected function _getFileNameByClass($class)
    {
        $file = null;
        if (Utility_Files::init()->classFileExists($class) /*&& !Utility_Classes::isVirtual($class)*/) {
            $path = implode(DIRECTORY_SEPARATOR, explode('_', $class)) . '.php';
            $directories = array('/app/code/', '/lib/');
            foreach ($directories as $dir) {
                $fullPath = str_replace('/', DIRECTORY_SEPARATOR,
                    Utility_Files::init()->getPathToSource() . $dir . $path
                );
                /**
                 * Use realpath() instead of file_exists() to avoid incorrect work on Windows because of case
                 *  insensitivity of file names
                 */
                if (realpath($fullPath) == $fullPath) {
                    $fileContent = file_get_contents($fullPath);
                    if (strpos($fileContent, 'class ' . $class) !== false ||
                        strpos($fileContent, 'interface ' . $class) !== false
                    ) {
                        $file = $fullPath;
                        break;
                    }
                }
            }
        }

        return $file;
    }

    /**
     * Get parent constructor data for a class declared in $content
     *
     * @param string $content
     * @return array|null|string|MethodReflection
     * @throws Exception
     */
    protected function _getParentConstructorData($content)
    {
        $parentData = null;
        $loop = 0;
        while (true) {
            if ($loop++ >= 100) {
                throw new Exception('Infinite loop emergency exit.');
            }

            $parentClass = $this->_getParentClass($content);
            if (!$parentClass) {
                echo sprintf('* Class "%s" has no parent', $this->_getClass($content, false)).PHP_EOL;
                return null;
            }

            if (class_exists($parentClass, false)) {
                // Class file is included or is original PHP. Use reflection or just skip.
                echo sprintf('* Class "%s" exists. It\'s either internal PHP class or was already included. ',
                    $parentClass
                ).PHP_EOL;
                $reflection = new MethodReflection($parentClass, '__construct');
                return $reflection;
            }

            if (isset(self::$_existingClasses[$parentClass])) {
                //NOTE: usage of cache decreases the number of disk operations and execution time,
                //  but increases memory usage
                return self::$_existingClasses[$parentClass];
            }

            $parentContent = $this->_getFileContentByClass($parentClass);
            try {
                $parentData = $this->_getConstructorData($parentContent);
            } catch (Exception $e) {
                throw new Exception('Check parent class for error: ' . $e->getMessage());
            }
            if ($parentData !== array()) {
                break;
            } else {
                $content = $parentContent;
            }
        }

        return $parentData;
    }

    /**
     * Check if the given types are exchangeable
     *
     * @param string $parentType Type of param used in parent method
     * @param string $childType Type of param used in child method
     * @return bool
     */
    protected function _isSubclassOf($parentType, $childType)
    {
        if ($parentType == $childType) {
            $isSubClass = true;
        } elseif ($parentType == 'array' || $childType == 'array') {
            $isSubClass = false;
        } else {
            $parents = $this->_getClassParents($childType);
            $isSubClass = in_array($parentType, $parents);
        }

        return $isSubClass;
    }

    /**
     * Get an array of parent classes for given class
     *
     * @param string $class
     * @return string[]
     * @throws Exception
     */
    protected function _getClassParents($class)
    {
        $level = 0;
        $parents = array();
        while (true) {
            if ($level++ > 20) {
                throw new Exception('Infinite loop emergency exit.');
            }
            $content = $this->_getFileContentByClass($class);
            $parent = $this->_getParentClass($content);

            if (!$parent) {
                break;
            }
            $parents[] = $parent;
            $class = $parent;
        }

        return $parents;
    }

    /**
     * Get content of a file where given class is declared
     *
     * @param string $class
     * @return string
     * @throws Exception
     */
    protected function _getFileContentByClass($class)
    {
        $fileName = $this->_getFileNameByClass($class);
        if ($fileName === null) {
            throw new Exception(
                sprintf('Impossible to resolve file by class "%s"', $class)
            );
        }

        $content = file_get_contents($fileName);
        if (!$content) {
            throw new Exception(
                sprintf('Impossible to fetch content of a file "%s" that should contain class "%s"', $fileName, $class)
            );
        }

        return $content;
    }

    /**
     * Check if method params match
     *
     * @param array|MethodReflection $parentSignature
     * @param array|MethodReflection $childSignature
     * @throws Exception
     */
    protected function _assertSignatureMatch($parentSignature, $childSignature)
    {
        $parentParams = $this->_extractParams($parentSignature);
        $childParams = $this->_extractParams($childSignature);

        //1. assures that there is no such case
        //  function func1($param1, $param2 = null, $param3)
        $this->_assertRequiredParamsPlacedBeforeOptionalOnes($parentParams);
        $this->_assertRequiredParamsPlacedBeforeOptionalOnes($childParams);

        foreach ($parentParams as $index => $parentParam) {
            $isRequired = !isset($parentParam['is_optional']);
            if (!$isRequired) {
                break;
            }

            $this->_assertRequiredParamExists($parentParam, $index, $childParams);

            $childParam = $childParams[$index];

            $this->_assertParamsAreNamedEqually($childParam, $parentParam);
            $this->_assertParamIsRequired($childParam);

            if (isset($parentParam['type'])) {
                $this->_assertParamHasType($childParam, $parentParam['type']);
                $this->_assertParamTypeMatch($childParam, $parentParam['type']);
            }
        }
    }

    /**
     * Assert required params are placed before optional ones in the list
     *
     * @param array $params
     * @throws Exception
     */
    protected function _assertRequiredParamsPlacedBeforeOptionalOnes($params)
    {
        $isOptionalParams = false;
        $firstOptionalParam = null;
        foreach ($params as $parentParam) {
            $parent = isset($parentParam['type'])
                ? $parentParam['type'] . ' ' . $parentParam['name']
                : $parentParam['name'];

            //1. assures that there is no such case
            //  function func1($param1, $param2 = null, $param3)
            $isOptional = isset($parentParam['is_optional']);
            if ($isOptional) {
                $isOptionalParams = true;
                $firstOptionalParam = $parent;
            } elseif ($isOptionalParams) {
                throw new Exception(sprintf(
                    'Required param (without default value) "%s" can not go after optional param(%s)',
                    $parent, $firstOptionalParam
                ));
            }
        }
    }

    /**
     * Check param with given index exists
     *
     * Assures that there is no such case:
     *      parent: function func1($param1, $param2)
     *      child: function func1($param1)
     * @param array $needle
     * @param integer $index
     * @param array $haystack
     * @throws Exception
     */
    protected function _assertRequiredParamExists($needle, $index, $haystack)
    {
        $parent = isset($needle['type'])
            ? $needle['type'] . ' ' . $needle['name']
            : $needle['name'];

        if (!isset($haystack[$index])) {
            throw new Exception(sprintf('Missing required param "%s"', $parent));
        }
    }

    /**
     * Check if param names match
     *
     * Assures that there is no such case:
     *  parent: function func1($param1)
     *  child: function func1($param2)
     *
     * @param array $paramToCheck
     * @param array $paramToCheckAgainst
     * @throws Exception
     */
    protected function _assertParamsAreNamedEqually($paramToCheck, $paramToCheckAgainst)
    {
        $checked = isset($paramToCheck['type'])
            ? $paramToCheck['type'] . ' ' . $paramToCheck['name']
            : $paramToCheck['name'];

        $expected = isset($paramToCheckAgainst['type'])
            ? $paramToCheckAgainst['type'] . ' ' . $paramToCheckAgainst['name']
            : $paramToCheckAgainst['name'];

        if ($paramToCheckAgainst['name'] != $paramToCheck['name']) {
            throw new Exception(sprintf('Invalid param "%s" found. Expected param "%s".',
                $checked,
                $expected
            ));
        }
    }

    /**
     * Check if param is required (has no default value)
     *
     * Assures that there is no such case:
     *  parent: function func1($param1)
     *  child: function func1($param1 = null)
     *
     * @param array $param
     * @throws Exception
     */
    protected function _assertParamIsRequired($param)
    {
        $paramInfo = isset($param['type'])
            ? $param['type'] . ' ' . $param['name']
            : $param['name'];

        if (isset($param['value'])) {
            throw new Exception(sprintf('Required param "%s" can not have default value "%s".',
                $paramInfo,
                $param['value']
            ));
        }
    }

    /**
     * Check if param has type at all
     *
     * Assures that there is no such case:
     *  parent: function func1(Class1 $param1)
     *  child: function func1($param1)
     *
     * @param array $param
     * @param string $expectedType
     * @throws Exception
     */
    protected function _assertParamHasType($param, $expectedType)
    {
        if (!isset($param['type'])) {
            throw new Exception(sprintf('Missing type for param "%s". Expected type "%s".',
                $param['name'], $expectedType
            ));
        }
    }

    /**
     * Check if param could be used as a substitution for a param of given type
     *
     * Assures that there is no such case:
     *  parent: function func1(Class1 $param1)
     *  child: function func1(Class2 $param1)
     *
     * @param array $param
     * @param string $type
     * @throws Exception
     */
    protected function _assertParamTypeMatch($param, $type)
    {
        if (
            isset($param['type']) && !$this->_isSubclassOf($type, $param['type'])
        ) {
            throw new Exception(sprintf(
                'Invalid type "%s" for param "%s". Expected type "%s" or it\'s subclass.',
                $param['type'], $param['name'], $type
            ));
        }
    }

    /**
     * Extract params out of signature
     *
     * @param MethodReflection|array $signature
     * @return array
     */
    protected function _extractParams($signature)
    {
        if ($signature instanceof MethodReflection) {
            $params = $this->_getMethodParams($signature);
        } else {
            $params = $signature['params'];
        }

        return $params;
    }

    /**
     * Check if method invocation matches method signature
     *
     * @param array|MethodReflection $methodData
     * @param array $invocationData
     */
    protected function _assertInvocationMatch($methodData, $invocationData)
    {
        if ($methodData instanceof MethodReflection) {
            $methodParams = $this->_getMethodParams($methodData);
        } else {
            $methodParams = $methodData['params'];
        }

        $countRequiredParams = 0;
        foreach ($methodParams as $param) {
            if (!$param['is_optional']) {
                $countRequiredParams++;
            }
        }
        $this->assertTrue(count($invocationData) >= $countRequiredParams,
            'Parent constructor invocation has to few params'
        );
    }

    /**
     * Extract array of method params out of MethodReflection object
     *
     * @param MethodReflection $parentSignature
     * @return array
     */
    protected function _getMethodParams(MethodReflection $parentSignature)
    {
        $parentParams = array();
        /** @var MethodReflection $parentSignature */
        /** @var ReflectionParameter $param */
        foreach ($parentSignature->getParameters() as $param) {
            $array = array();
            $array['name'] = '$' . $param->getName();
            if (isset($array['type'])) {
                $array['type'] = $param->getClass();
            }
            try {
                $array['value'] = $param->getDefaultValue();
            } catch (ReflectionException $e) {
                $array['value'] = '__internal__';
                echo sprintf('* Cannot determine default value for internal function "%s::%s()',
                    $parentSignature->class, $parentSignature->getName()
                ).PHP_EOL;
            }
            $array['is_optional'] = $param->isOptional();

            $parentParams[] = $array;
        }

        return $parentParams;
    }

    /**
     * Parse parent constructor invocation params out of the child constructor body
     *
     * @param string $body
     * @return array|null
     */
    protected function _getParentConstructorInvocationData($body)
    {
        $pattern = self::PATTERN_PARENT_CONSTRUCTOR_INVOCATION;
        preg_match($pattern, $body, $matches);

        if (!$matches) {
            return null;
        }

        $indexParamsString = 1;

        if (!isset($matches[$indexParamsString])) {
            return null;
        }

        $paramsString = $matches[$indexParamsString];

        $array = preg_split('~,\s+~', trim($paramsString));
        $vars = array();
        $pattern = sprintf('~%s~', self::PATTERN_PART_VARIABLE);
        foreach ($array as $variable) {
            if (!preg_match($pattern, $variable)) {
                return null;
            } else {
                $vars[] = $variable;
            }
        }

        return  $vars;
    }
}
