<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Formatter\PrettyPrinter;

class PrinterControlsTest extends TestBase
{
    /**
     * This method tests for loops.
     *
     * @dataProvider dataLoops
     */
    public function testLoops($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    /**
     * Provide data to test method
     *
     * @return array
     */
    public function dataLoops()
    {
        return array(
            array(
                "<?php class F1 {public function a(){foreach (\$as as \$k=>\$a){echo 'hi';}}}",
                "<?php\nclass F1\n{\n    public function a()\n    {\n        foreach (\$as as \$k => \$a) {\n" .
                "            echo 'hi';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class F1 {public function a(){foreach (\$as as \$k=>\$a){break 2;}}}",
                "<?php\nclass F1\n{\n    public function a()\n    {\n        foreach (\$as as \$k => \$a) {\n" .
                "            break 2;\n        }\n    }\n}\n"
            ),
            array(
                "<?php class F1 {public function a(){foreach (\$as as \$k=>\$a){continue 22;}}}",
                "<?php\nclass F1\n{\n    public function a()\n    {\n        foreach (\$as as \$k => \$a) {\n" .
                "            continue 22;\n        }\n    }\n}\n"
            ),
            array(
                "<?php class F2 {public function b(){for(\$a;\$a;\$a){echo 'hi';}}}",
                "<?php\nclass F2\n{\n    public function b()\n    {\n        for (\$a; \$a; \$a) {\n" .
                "            echo 'hi';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class L1 {public function a(){while(\$a){echo 'hi';}}}",
                "<?php\nclass L1\n{\n    public function a()\n    {\n        while (\$a) {\n" .
                "            echo 'hi';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class L2 {public function a(){do{echo 'hi';}while(\$a);}}",
                "<?php\nclass L2\n{\n    public function a()\n    {\n        do {\n" .
                "            echo 'hi';\n        } while (\$a);\n    }\n}\n"
            ),
            array(
                "<?php class L2 {public function a(){try{echo 'hi';}catch(\\Exception \$e){echo 'lo';}}}",
                "<?php\nclass L2\n{\n    public function a()\n    {\n        try {\n" .
                "            echo 'hi';\n        } catch (\\Exception \$e) {\n            echo 'lo';\n        }\n" .
                "    }\n}\n"
            ),
        );
    }

    /**
     * This method tests for the if loops.
     *
     * @dataProvider dataIf
     */
    public function testIf($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    /**
     * Provide data to test method
     *
     * @return array
     */
    public function dataIf()
    {
        return array(
            array(
                "<?php class If1 {public function a(){if (\$b) {echo 'hi';}}}",
                "<?php\nclass If1\n{\n    public function a()\n    {\n        if (\$b) {\n" .
                "            echo 'hi';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class If2 {public function b(){if (\$b) {echo 'hi';}elseif (\$c){echo 'lo';}}}",
                "<?php\nclass If2\n{\n    public function b()\n    {\n        if (\$b) {\n" .
                "            echo 'hi';\n        } elseif (\$c) {\n            echo 'lo';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class If3 {public function b(){if (\$b) {echo 'hi';}else{echo 'lo';}}}",
                "<?php\nclass If3\n{\n    public function b()\n    {\n        if (\$b) {\n" .
                "            echo 'hi';\n        } else {\n            echo 'lo';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class If4 {public function d(){if (\$b) {echo 'hi';}elseif (\$c){echo 'lo';}else{echo 'e';}}}",
                "<?php\nclass If4\n{\n    public function d()\n    {\n        if (\$b) {\n" .
                "            echo 'hi';\n        } elseif (\$c) {\n            echo 'lo';\n        } else {\n" .
                "            echo 'e';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class Sc1 {public function a(){switch(\$a){case 1:break;default:break;}}}",
                "<?php\nclass Sc1\n{\n    public function a()\n    {\n        switch (\$a) {\n" .
                "            case 1:\n                break;\n            default:\n                break;\n" .
                "        }\n    }\n}\n"
            ),
        );
    }

    /**
     * This method tests for controls.
     *
     * @dataProvider dataControls
     */
    public function testControls($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    /**
     * Provide data to test method
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataControls()
    {
        $originalCodeSnippet = <<<ORIGINALCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;
class TestClass {
    public function main(\$abcdefghijklmnopqrstuvwxyz) {
        if (isset(\$abcdefghijklmnopqrstuvwxyz)&&isset(\$abcdefghijklmnopqrstuvwxyz)&&
            isset(\$abcdefghijklmnopqrstuvwxyz)) {
            \$callback = 'hello';
            \$callback = 'good';
            \$callback = 'bye';
            if (isset(\$abcdefghijklmnopqrstuvwxyz)) {
                \$callback = 'asdf';
            }
        }
    }
}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet = <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;

class TestClass
{
    public function main(\$abcdefghijklmnopqrstuvwxyz)
    {
        if (isset(
            \$abcdefghijklmnopqrstuvwxyz
        ) && isset(\$abcdefghijklmnopqrstuvwxyz) && isset(\$abcdefghijklmnopqrstuvwxyz)
        ) {
            \$callback = 'hello';
            \$callback = 'good';
            \$callback = 'bye';
            if (isset(\$abcdefghijklmnopqrstuvwxyz)) {
                \$callback = 'asdf';
            }
        }
    }
}

FORMATTEDCODESNIPPET;
        $originalCodeSnippet2 = <<<ORIGINALCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;
class TestClass {
    public function main(\$results) {
        if (strcasecmp('FALSE', \$results) === 0 || strcasecmp('TRUE', \$results) === 0 ||
            strcasecmp('NULL', \$results) === 0) {
            \$tokens[sizeof(\$tokens) - 1] = strtolower(\$results);
            // reset the last item in the array due to php's "copy-on-write" rule for arrays
            \$treeNode->getData()->line->setTokens(\$tokens);
        }
    }
}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet2 = <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;

class TestClass
{
    public function main(\$results)
    {
        if (strcasecmp(
            'FALSE',
            \$results
        ) === 0 || strcasecmp('TRUE', \$results) === 0 || strcasecmp('NULL', \$results) === 0
        ) {
            \$tokens[sizeof(\$tokens) - 1] = strtolower(\$results);
            // reset the last item in the array due to php's "copy-on-write" rule for arrays
            \$treeNode->getData()->line->setTokens(\$tokens);
        }
    }
}

FORMATTEDCODESNIPPET;
        $originalCodeSnippet3 = <<<ORIGINALCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;
class TestArrayParameter {
    public function main(\$results) {
        \$element->setDisabled(array(\Magento\Catalog\Model\Session::DISPLAY_CATEGORY_PAGE,
                \Magento\Catalog\Model\Session::DISPLAY_PRODUCT_PAGE));
    }
}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet3 = <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;

class TestArrayParameter
{
    public function main(\$results)
    {
        \$element->setDisabled(
            array(
                \Magento\Catalog\Model\Session::DISPLAY_CATEGORY_PAGE,
                \Magento\Catalog\Model\Session::DISPLAY_PRODUCT_PAGE
            )
        );
    }
}

FORMATTEDCODESNIPPET;
        $originalCodeSnippet4 = <<<ORIGINALCODESNIPPET
<?php
class TestIfCase{
    public function main(\$results) {
        if (\$otherCode) {
            \$files = array_merge(
                \$files,
                glob(\$this->_path . '/*.php', GLOB_NOSORT),
                glob(\$this->_path . '/pub/*.php', GLOB_NOSORT),
                self::getFiles(array("{\$this->_path}/downloader"), '*.php'),
                self::getFiles(array("{\$this->_path}/lib/{Mage,Magento,Varien}"), '*.php')
            );
        }}}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet4 = <<<FORMATTEDCODESNIPPET
<?php
class TestIfCase
{
    public function main(\$results)
    {
        if (\$otherCode) {
            \$files = array_merge(
                \$files,
                glob(\$this->_path . '/*.php', GLOB_NOSORT),
                glob(\$this->_path . '/pub/*.php', GLOB_NOSORT),
                self::getFiles(array("{\$this->_path}/downloader"), '*.php'),
                self::getFiles(array("{\$this->_path}/lib/{Mage,Magento,Varien}"), '*.php')
            );
        }
    }
}

FORMATTEDCODESNIPPET;
        $originalCodeSnippet5 = <<<ORIGINALCODESNIPPET
<?php
function alpha() {
if (\$ftp) {
    \$cwd=\$ftpObj->getcwd();
    \$dir=\$cwd . DIRECTORY_SEPARATOR .\$config->downloader_path . DIRECTORY_SEPARATOR
        . \Magento\Connect\Config::DEFAULT_CACHE_PATH . DIRECTORY_SEPARATOR . trim( \$pChan, "\\/");
    \$ftpObj->mkdirRecursive(\$dir,0777);
    \$ftpObj->chdir(\$cwd);
} else {
    \$dir = \$config->getChannelCacheDir(\$pChan);
    @mkdir(\$dir, 0777, true);
}}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet5 = <<<FORMATTEDCODESNIPPET
<?php
function alpha()
{
    if (\$ftp) {
        \$cwd = \$ftpObj->getcwd();
        \$dir = \$cwd .
            DIRECTORY_SEPARATOR .
            \$config->downloader_path .
            DIRECTORY_SEPARATOR .
            \Magento\Connect\Config::DEFAULT_CACHE_PATH .
            DIRECTORY_SEPARATOR .
            trim(
            \$pChan,
            "\\/"
        );
        \$ftpObj->mkdirRecursive(\$dir, 0777);
        \$ftpObj->chdir(\$cwd);
    } else {
        \$dir = \$config->getChannelCacheDir(\$pChan);
        @mkdir(\$dir, 0777, true);
    }
}

FORMATTEDCODESNIPPET;


        return array(
            array(
                "<?php class Con1 {public function a(){array_merge(\$a,function(){echo 'hi';});}}",
                "<?php\nclass Con1\n{\n    public function a()\n    {\n" .
                "        array_merge(\n            \$a,\n            function () {\n                echo 'hi';\n" .
                "            }\n        );\n    }\n}\n"
            ),
            array(
                "<?php class Con2 {public function a(){array_merge(\$a,function()use(\$a,\$b){echo 'hi';});}}",
                "<?php\nclass Con2\n{\n    public function a()\n    {\n        array_merge(\n            \$a,\n" .
                "            function () use (\$a, \$b) {\n                echo 'hi';\n" .
                "            }\n        );\n    }\n}\n"
            ),
            array($originalCodeSnippet, $formattedCodeSnippet),
            array($originalCodeSnippet2, $formattedCodeSnippet2),
            array($originalCodeSnippet3, $formattedCodeSnippet3),
            array($originalCodeSnippet4, $formattedCodeSnippet4),
            array($originalCodeSnippet5, $formattedCodeSnippet5),
        );
    }
}
