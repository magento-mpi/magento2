<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Rdbms
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * PhpDoc comment parser
 *
 * @category    Mage
 * @package     Mage_Rdbms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rdbms_PhpDoc
{
    /**
     * Array of method parameters
     *
     * @var array
     */
    protected $_params     = array();

    /**
     * Additional comments array of method parameters
     *
     * @var array
     */
    protected $_paramsAdd  = array();

    /**
     * Array of method / property comments (descriptions)
     *
     * @var array
     */
    protected $_comments   = array();

    /**
     * Property data type
     *
     * @var string
     */
    protected $_var        = 'unknown';

    /**
     * Array of other PhpDoc tags
     *
     * @var array
     */
    protected $_docs       = array();

    /**
     * The return value data type of functions or methods
     *
     * @var string
     */
    protected $_return     = false;

    /**
     * Parse PhpDoc comment block
     *
     * @param string $comment
     */
    public function __construct($comment)
    {
        foreach (split("\n", $comment) as $line) {
            $line = trim($line);
            if (preg_match('#^[\/\*]+$#', $line)) {
                continue;
            }
            $match = array();
            if (preg_match('#@return\s+(.*)#', $line, $match)) {
                $this->_return = $match[1];
                continue;
            }

            if (preg_match('#@var\s(.*)#', $line, $match)) {
                $this->_var = $match[1];
                continue;
            }

            if (preg_match('#@param\s(.*)\s\$([A-Z0-9a-z_]+)(.*)?#', $line, $match)) {
                $this->_params[$match[2]] = trim($match[1]);
                if (!empty($match[3])) {
                    $this->_paramsAdd[$match[2]] = $match[3];
                }
                continue;
            }
            if (preg_match('#@([a-z]+)\s+(.*)#', $line, $match)) {
                $this->_docs[$match[1]] = $match[2];
                continue;
            }

            if (preg_match('#^\* (.*)#', $line, $match)) {
                $this->_comments[] = $match[1];
                continue;
            }
        }
    }

    /**
     * Retrieve function or method parameter data type
     *
     * @param string $key   the parameter name
     * @return string|false
     */
    public function getParam($key)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }
        return 'unknown_type';
    }

    /**
     * Returns function or method parameter comment
     *
     * @param string $key
     * @return string
     */
    public function getParamComment($key)
    {
        if (isset($this->_paramsAdd[$key])) {
            return $this->_paramsAdd[$key];
        }
        return '';
    }

    /**
     * Returns array of comments
     *
     * @return array
     */
    public function getComments()
    {
        return $this->_comments;
    }

    /**
     * Return array of other PhpDoc tags
     * Tag-value pairs
     *
     * @return array
     */
    public function getDocs()
    {
        return $this->_docs;
    }

    /**
     * Returns property data type
     *
     * @return string
     */
    public function getVar()
    {
        return $this->_var;
    }

    /**
     * Returns the return value data type of function or method
     *
     * @return string
     */
    public function getReturn()
    {
        return $this->_return;
    }

    /**
     * Gets other tag property by tag name
     *
     * @param string $key
     * @return string|false
     */
    public function getDoc($key)
    {
        if (isset($this->_docs[$key])) {
            return $this->_docs[$key];
        }
        return false;
    }
}


/**
 * Mage Resource reflection class
 *
 * @category    Mage
 * @package     Mage_Rdbms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rdbms_Resource
{
    /**
     * Reflection Class instance
     *
     * @var ReflectionClass
     */
    protected $_rc;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_className;

    /**
     * Class file content
     *
     * @var string
     */
    protected $_classContent;

    /**
     * Class content as array (per line)
     *
     * @var array
     */
    protected $_classContentArray;

    /**
     * Changes Class name by dictionary
     *
     * @var array
     */
    protected $_classMap           = array(
        'Mage_Core_Model_Mysql4_Abstract'               => 'Mage_Core_Model_Resource_Db_Abstract',
        'Mage_Core_Model_Mysql4_Collection_Abstract'    => 'Mage_Core_Model_Resource_Db_Collection_Abstract',
        'Mage_Catalog_Model_Resource_Eav_Mysql4'        => 'Mage_Catalog_Model_Resource',
        'Mage_Customer_Model_Entity_'                   => 'Mage_Customer_Model_Resource_',
        '_Model_Mysql4'                                 => '_Model_Resource',
    );

    /**
     * new class declaration Output
     *
     * @var string
     */
    protected $_out;

    /**
     * Initialize class
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->_className           = $className;
        $this->_rc                  = new ReflectionClass($this->_className);
        $this->_classContent        = file_get_contents($this->_rc->getFileName());
        $this->_classContentArray   = split("\n", $this->_classContent);
    }

    /**
     * Returns class name
     *
     * @param string|ReflectionClass $className
     * @return string
     */
    public function _getClassName($className)
    {
        if ($className instanceof ReflectionClass) {
            $className = $className->getName();
        }

        return strtr($className, $this->_classMap);
    }

    /**
     * Returns escaped for class string
     *
     * @param mixed $string
     * @return string
     */
    protected function _escape($string)
    {
        if (is_null($string)) {
            return 'null';
        }
        if (is_bool($string)) {
            return $string === true ? 'true' : 'false';
        }
        if (is_numeric($string)) {
            return $string;
        }
        if (is_array($string)) {
            if (empty($string)) {
                return 'array()';
            } else {
                /**
                 * @todo
                 */
                $result = var_export($string,true);
                return preg_replace(array("/\n/","/\s\s+/","/,\)/"), array('', ' ',')'), $result);
            }
        }

        if (is_string($string)) {
            return sprintf("'%s'", str_replace("'", "\\'", $string));
        }

        return false;
    }

    /**
     * Draw license and class PHPDoc
     *
     */
    protected function _drawLicence($deprecated = false)
    {
        $class   = split('_', $this->_className);
        $phpDoc  = new Mage_Rdbms_PhpDoc($this->_rc->getDocComment());
        $comment = join("\n * ", $phpDoc->getComments());
        if (!$comment || strpos($comment, 'NOTICE OF LICENSE') !== false) {
            $comment = 'Enter description here ...';
        }
        if (!$deprecarted) {
            if ($class[0] == "Enterprise") {
                $license = <<<LICENSE

/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    %1\$s
 * @package     %1\$s_%2\$s
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * %3\$s
 *
 * @category    %1\$s
 * @package     %1\$s_%2\$s
 * @author      Magento Core Team <core@magentocommerce.com>
 */

LICENSE;
            } else {
                $license = <<<LICENSE

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    %1\$s
 * @package     %1\$s_%2\$s
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * %3\$s
 *
 * @category    %1\$s
 * @package     %1\$s_%2\$s
 * @author      Magento Core Team <core@magentocommerce.com>
 */

LICENSE;
            }
        } else {
            if ($class[0] == "Enterprise") {
                $license = <<<LICENSE
$license = <<<LICENSE

/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    %1\$s
 * @package     %1\$s_%2\$s
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * %3\$s
 *
 * @category    %1\$s
 * @package     %1\$s_%2\$s
.* @deprecated  since 1.10
 * @author      Magento Core Team <core@magentocommerce.com>
 */

LICENSE;
            } else {
                $license = <<<LICENSE

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    %1\$s
 * @package     %1\$s_%2\$s
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * %3\$s
 *
 * @category    %1\$s
 * @package     %1\$s_%2\$s
 * @deprecated  since 1.5
 * @author      Magento Core Team <core@magentocommerce.com>
 */

LICENSE;
            }
        }
        $this->_out = sprintf('<?php' . $license, $class[0], $class[1], $comment);
    }

    /**
     * Draw class header to output
     *
     */
    protected function _drawClassHeader()
    {
        $parts = array();
        $class = null;
        if ($this->_rc->isInterface()) {
            $class = 'interface ';
        } else {
            if ($this->_rc->isAbstract()) {
                $class = 'abstract ';
            }
            $class .= 'class ';
        }
        $class .= $this->_getClassName($this->_rc->name);
        $parts[] = $class;

        // extends
        if (false !== ($parent = $this->_rc->getParentClass())) {
            $parts[] = sprintf(' extends %s', $this->_getClassName($parent));
        }

        $match = array();
        if (false !== ($interfaces = $this->_rc->getInterfaceNames())) {
            foreach ($interfaces as $interface) {
                $regExp = '#implements\s+' . preg_quote($interface) . '#';
                if (preg_match($regExp, $this->_classContent, $match)) {
                    $parts[] = sprintf(' implements %s', $this->_getClassName($interface));
                }
            }
        }

        $length = 0;
        foreach ($parts as $part) {
            if ($length + strlen($part) > 120) {
                $length = 3 + strlen($part);
                $this->_out .= "\n   {$part}";
            } else {
                $this->_out .= $part;
                $length += strlen($part);
            }
        }

        $this->_out .= "\n{\n";
    }

    protected function _drawDummyClassHeader()
    {
        $parts = array();
        $parts[] = 'class ' . $this->_className;
        $parts[] = ' extends ' . $this->_getClassName($this->_rc->name);
        $length = 0;
        foreach ($parts as $part) {
            if ($length + strlen($part) > 120) {
                $length = 3 + strlen($part);
                $this->_out .= "\n   {$part}";
            } else {
                $this->_out .= $part;
                $length += strlen($part);
            }
        }

        $this->_out .= "\n{\n";
    }
    /**
     * Draw class footer to output
     *
     */
    protected function _drawClassFooter()
    {
        $this->_out = rtrim($this->_out, "\n") . "\n}\n";
    }

    /**
     * Draw constants of class to output
     *
     */
    protected function _drawConstants()
    {
        $constants  = array();
        $maxLen     = 0;
        //echo $this->_className."\n";
        foreach ($this->_rc->getConstants() as $k => $v) {
            if (strpos($this->_classContent, $k) === false) {
                continue;
            }

            $constants[$k] = $v;

            if (strlen($k) > $maxLen) {
                $maxLen = strlen($k);
            }
        }

        if (!$constants) {
            return ;
        }

        $maxLen += (($i = $maxLen % 4) == 0 ? 4 : $i) - 2;
        foreach ($constants as $k => $v) {
            $this->_out .= sprintf("    const %-{$maxLen}s= %s;\n", $k, $this->_escape($v));
        }

        $this->_out .= "\n";
    }

    /**
     * Draw properties of class
     *
     */
    protected function _drawProperties()
    {
        // calculate max property length and get default values
        $maxLen     = 0;
        $defValues  = array(); // property default values
        foreach ($this->_rc->getProperties() as $prop) {
            /* @var $prop ReflectionProperty */
            if ($prop->getDeclaringClass()->getName() != $this->_className) {
                continue;
            }

            $prefix = '';
            if ($prop->isProtected()) {
                $prefix .= 'protected';
            } else if ($prop->isPrivate()) {
                $prefix .= 'private';
            } else if ($prop->isPublic()) {
                $prefix .= 'public';
            }
            if ($prop->isStatic()) {
                $prefix .= ' static';
            }

            $declaration = $prefix . ' $' . $prop->name;

            if (strlen($declaration) > $maxLen) {
                $maxLen = strlen($declaration);
            }

            $match  = array();
            $regExp = '#\$'.preg_quote($prop->name).'\s+?=\s+?([^;]+);#siU';
            if (preg_match($regExp, $this->_classContent, $match)) {
                $defValues[$prop->name] = $match[1];
            }
        }

        // draw properties
        $maxLen += (($i = $maxLen % 4) == 0 ? 4 : $i);
        foreach ($this->_rc->getProperties() as $prop) {
            /* @var $prop ReflectionProperty */
            if ($prop->getDeclaringClass()->getName() != $this->_className) {
                continue;
            }

            $this->_drawPropertyPhpDoc($prop);

            $prefix = '';
            if ($prop->isProtected()) {
                $prefix .= 'protected';
            } else if ($prop->isPrivate()) {
                $prefix .= 'private';
            } else if ($prop->isPublic()) {
                $prefix .= 'public';
            }
            if ($prop->isStatic()) {
                $prefix .= ' static';
            }

            $declaration = sprintf('%s $%s', $prefix, $prop->name);
            $default     = null;

            if (isset($defValues[$prop->name])) {
                $default = sprintf(' = %s;', $defValues[$prop->name]);
                $this->_out .= sprintf("    %-{$maxLen}s%s\n\n", $declaration, $default);
            } else {
                $this->_out .= sprintf("    %s;\n\n", $declaration);
                $declaration .= ';';
            }
        }
    }

    /**
     * Draw PhpDoc line for class property
     *
     * @param ReflectionProperty $property
     */
    protected function _drawPropertyPhpDoc(ReflectionProperty $property)
    {
        $output = '';
        $phpDoc = new Mage_Rdbms_PhpDoc($property->getDocComment());

        $output .= "    /**\n";
        foreach ($phpDoc->getComments() as $comment) {
            $output .= "     * {$comment}\n";
        }
        if (!$phpDoc->getComments()) {
            $output .= "     * Enter description here ...\n";
        }
        $output .= "     *\n";
        $output .= "     * @var {$this->_getClassName($phpDoc->getVar())}\n";
        $output .= "     */\n";

        $this->_out .= $output;
    }

    /**
     * Draw methods of class to output
     *
     */
    protected function _drawMethods()
    {
        foreach ($this->_rc->getMethods() as $method) {
            /* @var $method ReflectionMethod */
            if ($method->getDeclaringClass()->getName() != $this->_className) {
                continue;
            }

            $methodContent = $this->_getMethodContent($method);
            $this->_drawMethodPhpDoc($method, $methodContent);

            $prefix = '';
            if ($method->isFinal()) {
                $prefix = 'final ';
            }
            if ($method->isAbstract() && !$this->_rc->isInterface()) {
                $prefix = 'abstract ';
            }
            if ($method->isProtected()) {
                $prefix .= 'protected';
            } else if ($method->isPrivate()) {
                $prefix .= 'private';
            } else if ($method->isPublic()) {
                $prefix .= 'public';
            }
            if ($method->isStatic()) {
                $prefix .= ' static';
            }

            $params = array();
            foreach ($method->getParameters() as $param) {
                $part = '';
                if ($param->isArray()) {
                    $part .= 'array ';
                }

                if ($param->getClass()) {
                    $part .= $this->_getClassName($param->getClass()) . ' ';
                }

                if ($param->isPassedByReference()) {
                    $part .= '&';
                }
                $part .= "\${$param->name}";

                if ($param->isOptional()) {
                    $part .= ' = ' . $this->_escape($param->getDefaultValue());
                }

                $params[] = $part;
            }

            $output = "    {$prefix} function {$method->name}(";
            $length = strlen($output);
            $i      = 0;
            $lines  = 0;
            foreach ($params as $part) {
                if ($i != 0) {
                    $output .= ', ';
                    $length += 2;
                }

                if ((strlen($part) + $length) > 120) {
                    $output .= "\n        {$part}";
                    $lines ++;
                    $length = strlen($part) + 8;
                } else {
                    $length += strlen($part);
                    $output .= $part;
                }
                $i ++;
            }

            if ($length < 119) {
                $output .= ")\n";
            } else {
                $output .= "\n        )\n";
            }

            if ($method->isAbstract()) {
                $output .= ";\n\n";
            } else {
                $output .= "    {\n";
                $output .= $methodContent;
                $output .= "    }\n\n";
            }

            $this->_out .= $output;
        }
    }

    /**
     * Retrieve method content
     *
     * @param ReflectionMethod $method
     * @return string
     */
    protected function _getMethodContent(ReflectionMethod $method)
    {
        $offset  = $method->getStartLine()-1;
        $length  = $method->getEndLine() - $method->getStartLine()+1;
        $content = array_slice($this->_classContentArray, $offset, $length);

        $content = join("\n", $content);
        $methodStart    = strpos($content, '{');
        if ($methodStart !== false) {
            $content = substr($content, $methodStart + 1);
        }
        $methodFinish   = strrpos($content, '}');
        if ($methodFinish !== false) {
            $content = substr($content, 0, $methodFinish - 1);
        }

        $content = str_repeat(' ', 8) . trim($content) . "\n";

        return preg_replace_callback('/\/\*+\s+\@var\s+\$([a-zA-Z0-9_]+)\s+([a-zA-Z0-9_]+)\s+\*+\//',
            array($this, '_callbackPhpDocVar'), $content);
    }

    /**
     * Call-back for PhpDoc fragment with tag @var
     *
     * @param array $matches
     * @return string
     */
    protected function _callbackPhpDocVar($match)
    {
        $newClass = $this->_getClassName($match[2]);
        if ($match[2] != $newClass) {
            return sprintf('/* @var $%s %s */', $match[1], $newClass);
        }
        return $match[0];
    }

    /**
     * Draw PhpDoc block for method of class to output
     *
     * @param ReflectionMethod $method
     * @param string $methodContent
     */
    protected function _drawMethodPhpDoc(ReflectionMethod $method, $methodContent = null)
    {
        $phpDoc = new Mage_Rdbms_PhpDoc($method->getDocComment());

        if (is_null($methodContent)) {
            $methodContent = $this->_getMethodContent($method);
        }

        $hasReturn  = false;
        $return     = null;
        $matches    = array();
        if (preg_match('#return\s+\$this;#', $methodContent)) {
            $hasReturn  = true;
            $return     = $this->_getClassName($this->_className);
        } else if (preg_match_all('#return([^;]+)?;#siU', $methodContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (trim($match[1]) != '' && trim($match[1]) != ';') {
                    $hasReturn  = true;
                }
            }
            if ($hasReturn) {
                $return = $phpDoc->getReturn();
                if ($return === false) {
                    $return = 'unknown';
                } else {
                    $return = $this->_getClassName($return);
                }
            }
        }

        $output = "    /**\n";
        foreach ($phpDoc->getComments() as $comment) {
            if (preg_match("/[\s\*]*\*\/(\s|$)/",$comment)) {
                continue;
            }
            $output .= "     * {$comment}\n";
        }
        if (!$phpDoc->getComments()) {
            $output .= "     * Enter description here ...\n";
        }
        $output .= "     *\n";

        if ($phpDoc->getDocs()) {
            foreach ($phpDoc->getDocs() as $k => $v) {
                if ($k == 'param') {
                    continue;
                }
                $output .= "     * @{$k} {$this->_getClassName($v)}\n";
            }
            $output .= "     *\n";
        }

        foreach ($method->getParameters() as $param) {
            /* @var $param ReflectionParameter */
            $type = $phpDoc->getParam($param->name);
            if ($type == 'unknown_type') {
                if ($param->isArray()) {
                    $type = 'array';
                }
                if ($param->getClass()) {
                    $type = $this->_getClassName($param->getClass());
                }
            } else {
                $type = $this->_getClassName($type);
            }
            $output .= sprintf("     * @param %s \$%s%s\n", $type, $param->name,
                $phpDoc->getParamComment($param->name));
        }

        if ($hasReturn) {
            $output .= "     * @return {$return}\n" ;
        }

        $output .= "     */\n";
        $this->_out .= $output;
    }

    /**
     * Drow or returns class declaration
     *
     * @param string $return
     * @return string|void
     */
    public function draw($return = true)
    {
        $this->_drawLicence();
        $this->_drawClassHeader();
        $this->_drawConstants();
        $this->_drawProperties();
        $this->_drawMethods();
        $this->_drawClassFooter();

        if ($return) {
            return $this->_out;
        }
        echo highlight_string($this->_out, true);
    }

    /**
     * Enter description here ...
     *
     */
    public function drawDummyClass($return = true)
    {
        $this->_out = '';
        $this->_drawLicence(true);
        $this->_drawDummyClassHeader();
        $this->_drawClassFooter();
        if ($return) {
            return $this->_out;
        }
        echo highlight_string($this->_out, true);
    }
}

class Mage_Rdbms_Convert
{
    /**
     * Output path
     *
     * @var string
     */
    protected $_outPath;

    /**
     * Skip classes
     *
     * @var array
     */
    protected $_skipClasses = array("Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Gallery");

    /**
     * Initialize convertor
     *
     * @param string $path  source magento path
     */
    public function __construct($path)
    {
        $mageFile = $path . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
        if (!file_exists($mageFile)) {
            throw new Exception('Invalid Magento path');
        }
        require_once $mageFile;

        // initialize application
        Mage::app();

        $this->_outPath = Mage::getBaseDir('code') . DS . 'core' . DS;
    }

    /**
     * Set output path
     *
     * @param string $path
     * @return Mage_Rdbms_Convert
     */
    public function setOutputDir($path)
    {
        $this->_outPath = $path;
        return $this;
    }

    /**
     * Add class to skip
     *
     * @param string $className
     * @return Mage_Rdbms_Convert
     */
    public function addSkipClass($className)
    {
        if (!in_array($className, $this->_skipClasses)) {
            $this->_skipClasses[] = $className;
        }

        return $this;
    }

    /**
     * Run convert
     *
     * @param string $path      path or module name
     * @param boolean $isModule
     */
    public function run($path, $isModule = false)
    {
        echo '<pre>'.$path."\r\n";

        if ($isModule) {
            switch ($path) {
                case 'Mage_Catalog':
                    $pathPart = str_replace('_', DS, $path) . DS . 'Model' . DS . 'Resource' . DS .
                        "Eav" . DS . 'Mysql4';
                    break;
                case 'Mage_Customer':
                    $pathPart = str_replace('_', DS, $path) . DS . 'Model' . DS . 'Entity';
                    break;
                default:
                    $pathPart = str_replace('_', DS, $path) . DS . 'Model' . DS . 'Mysql4';
                    break;
            }

            $path =  Mage::getBaseDir('code') . DS . 'core' . DS .$pathPart;
        }
        if (!is_dir($path)) {
            echo "BAD PATH:" . $path . "\n";
            return;
        }
        $path = rtrim(realpath($path), DS);
        foreach (glob($path . DS . '*') as $f) {

            if (is_file($f)) {
                if (preg_match('#((Mage|Enterprise)[^.]+)\.php#', $f, $match)) {
                    $className = join("_", explode(DS, $match[1]));
                    if (in_array($className, $this->_skipClasses)) {
                        continue;
                    }

                    try {
                        echo $className;
                        $r = new Mage_Rdbms_Resource($className);
                        $outfile = $this->_outPath . join(DS, explode("_", $r->_getClassName($className))).".php";
                        echo $outfile."\n";
                        $content = $r->draw(true);
                        Mage::getConfig()->createDirIfNotExists(dirname($outfile));
                        file_put_contents($outfile, $content);
                        if (!$this->checkFile($outfile,$error)) {
                            echo $error;
                            exit;
                        }
                        $dummyContent = $r->drawDummyClass(true);
                        file_put_contents($f, $dummyContent);

                    } catch (Exception $e) {
                        echo $e;
                    }
                }
            } else if (is_dir($f)) {
                $this->run($f);
            }
        }
    }

    public function checkFile($path, &$error)
    {
        exec("php -l $path",$error,$code);
        if ($code==0) {
            return true;
        }
        return false;
    }
}


$modules = array(
    "Mage_Core","Mage_Eav","Mage_Page","Mage_Install","Mage_Admin","Mage_Rule","Mage_Adminhtml",
    "Mage_AdminNotification","Mage_Cron","Mage_Directory",
    "Mage_Customer",
    "Mage_Catalog",
    "Mage_CatalogRule","Mage_CatalogIndex","Mage_CatalogSearch","Mage_Sales","Mage_SalesRule","Mage_Checkout",
    "Mage_Shipping","Mage_Payment","Mage_Usa","Mage_Paygate","Mage_Paypal","Mage_PaypalUk","Mage_GoogleCheckout",
    "Mage_Log","Mage_Backup","Mage_Poll","Mage_Rating","Mage_Review","Mage_Tag","Mage_Cms","Mage_Reports",
    "Mage_Tax","Mage_Wishlist","Mage_GoogleAnalytics","Mage_CatalogInventory","Mage_GiftMessage","Mage_Sendfriend",
    "Mage_Media","Mage_Sitemap","Mage_Contacts","Mage_Dataflow","Mage_Rss","Mage_ProductAlert","Mage_GoogleOptimizer",
    "Mage_GoogleBase","Mage_Index","Mage_AmazonPayments","Mage_Api","Mage_Bundle","Mage_Centinel","Mage_Chronopay",
    "Mage_Compiler","Mage_Connect","Mage_Cybermut","Mage_Cybersource","Mage_Downloadable","Mage_Eway","Mage_Flo2Cash",
    "Mage_Ideal","Mage_LoadTest","Mage_Ogone","Mage_Oscommerce","Mage_Paybox","Mage_Protx","Mage_Strikeiron",
    "Mage_Weee","Mage_Widget","Mage_XmlConnect","Enterprise_AdminGws","Enterprise_Banner","Mage_Newsletter",
    "Enterprise_CatalogEvent","Enterprise_CatalogPermissions","Enterprise_Checkout","Enterprise_Cms",
    "Enterprise_Customer","Enterprise_CustomerBalance","Enterprise_CustomerSegment","Enterprise_Enterprise",
    "Enterprise_GiftCard","Enterprise_GiftCardAccount","Enterprise_GiftRegistry","Enterprise_Invitation",
    "Enterprise_License","Enterprise_Logging","Enterprise_PageCache","Enterprise_Pbridge","Enterprise_Pci",
    "Enterprise_Reminder","Enterprise_Reward","Enterprise_SalesArchive",/*"Enterprise_SalesPool",*/"Enterprise_Search",
    "Enterprise_Staging","Enterprise_TargetRule","Enterprise_WebsiteRestriction"
);
$convert = new Mage_Rdbms_Convert('/home/else/Projects/mmdb/');

foreach ($modules as $moduleName) {
    //echo $moduleName."\n";
    $convert->run($moduleName, true);
}
