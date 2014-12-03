<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class PrinterOperatorsTest extends TestBase
{
    /**
     * This method tests arrays in the pretty printer.
     *
     * @dataProvider dataOperators
     */
    public function testOperators($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    /**
     * Provide data to test method
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataOperators()
    {
        return array(
            array("<?php\n\$d=(int)  1;", "<?php\n\$d = (int)1;\n"),
            array("<?php\n\$d=(double)  1;", "<?php\n\$d = (double)1;\n"),
            array("<?php\n\$d=(string)  1;", "<?php\n\$d = (string)1;\n"),
            array("<?php\n\$d=(array)  1;", "<?php\n\$d = (array)1;\n"),
            array("<?php\n\$d=(object)  1;", "<?php\n\$d = (object)1;\n"),
            array("<?php\n\$d=(bool) 1;", "<?php\n\$d = (bool)1;\n"),
            array("<?php\n\$d=(unset) 1;", "<?php\n\$d = (unset)1;\n"),
            array("<?php\n\$d=1<<1;", "<?php\n\$d = 1 << 1;\n"),
            array("<?php\n\$d=1>>1;", "<?php\n\$d = 1 >> 1;\n"),
            array("<?php\n\$d=1+1;", "<?php\n\$d = 1 + 1;\n"),
            array("<?php\n\$d=1*1;", "<?php\n\$d = 1 * 1;\n"),
            array("<?php\n\$d=1/1;", "<?php\n\$d = 1 / 1;\n"),
            array("<?php\n\$d=1-1;", "<?php\n\$d = 1 - 1;\n"),
            array("<?php\n\$d=5%2;", "<?php\n\$d = 5 % 2;\n"),
            array("<?php\n\$d=5&2;", "<?php\n\$d = 5 & 2;\n"),
            array("<?php\n\$d=5|2;", "<?php\n\$d = 5 | 2;\n"),
            array("<?php\n\$d=5^2;", "<?php\n\$d = 5 ^ 2;\n"),
            array("<?php\n\$d=1/1*2+1;", "<?php\n\$d = 1 / 1 * 2 + 1;\n"),
            array("<?php\n\$d=1/1*(2+1);", "<?php\n\$d = 1 / 1 * (2 + 1);\n"),
            array("<?php\n\$d=&\$refable;", "<?php\n\$d =& \$refable;\n"),
            array("<?php\n\$d+=2-3-4*(4+6);", "<?php\n\$d += 2 - 3 - 4 * (4 + 6);\n"),
            array("<?php\n\$d-=2-3-4*(4+6);", "<?php\n\$d -= 2 - 3 - 4 * (4 + 6);\n"),
            array("<?php\n\$d*=2-3-4*(4+6);", "<?php\n\$d *= 2 - 3 - 4 * (4 + 6);\n"),
            array("<?php\n\$d/=2-3-4*(4+6);", "<?php\n\$d /= 2 - 3 - 4 * (4 + 6);\n"),
            array("<?php\n\$d =2&&(3||4)&&(4&&6);", "<?php\n\$d = 2 && (3 || 4) && (4 && 6);\n"),
            array("<?php\n\$d.='tiger';", "<?php\n\$d .= 'tiger';\n"),
            array("<?php\n\$d.='tiger\\n';", "<?php\n\$d .= 'tiger\\n';\n"),
            array("<?php\n\$d.=\"tiger\\n\";", "<?php\n\$d .= \"tiger\\n\";\n"),
            array("<?php\n\$d%=2-3-4*(4+6);", "<?php\n\$d %= 2 - 3 - 4 * (4 + 6);\n"),
            array("<?php\n\$d&=\$bit;", "<?php\n\$d &= \$bit;\n"),
            array("<?php\n\$d|=\$bit;", "<?php\n\$d |= \$bit;\n"),
            array("<?php\n\$d^=\$bit;", "<?php\n\$d ^= \$bit;\n"),
            array("<?php\n\$d<<=\$bit;", "<?php\n\$d <<= \$bit;\n"),
            array("<?php\n\$d>>=\$bit;", "<?php\n\$d >>= \$bit;\n"),
            array("<?php\n\$d =\$ham and \$eggs;", "<?php\n\$d = \$ham and \$eggs;\n"),
            array("<?php\n\$d =\$ham xor \$eggs;", "<?php\n\$d = \$ham xor \$eggs;\n"),
            array("<?php\n\$d =\$ham or \$eggs;", "<?php\n\$d = \$ham or \$eggs;\n"),
            array("<?php\n\$d =\$ham or (\$eggs and \$a) xor \$b;", "<?php\n\$d = \$ham or \$eggs and \$a xor \$b;\n"),
            array(
                "<?php\n\$d =(\$ham or \$eggs) and \$a xor \$b;",
                "<?php\n\$d = (\$ham or \$eggs) and \$a xor \$b;\n"
            ),
            array("<?php\n\$d=~\$a;", "<?php\n\$d = ~\$a;\n"),
            array("<?php\n\$d=++\$a;", "<?php\n\$d = ++\$a;\n"),
            array("<?php\n\$d=@\$a;", "<?php\n\$d = @\$a;\n"),
            array("<?php\n\$d=22+ ++\$a;", "<?php\n\$d = 22 + ++\$a;\n"),
            array("<?php\n\$d=--\$a;", "<?php\n\$d = --\$a;\n"),
            array("<?php\n\$d=22+ --\$a;", "<?php\n\$d = 22 + --\$a;\n"),
            array("<?php\n\$d=\$a++;", "<?php\n\$d = \$a++;\n"),
            array("<?php\n\$d=22+ \$a++;", "<?php\n\$d = 22 + \$a++;\n"),
            array("<?php\n\$d=\$a--;", "<?php\n\$d = \$a--;\n"),
            array("<?php\n\$d=22+ \$a--;", "<?php\n\$d = 22 + \$a--;\n"),
            array("<?php\n\$d=+22;", "<?php\n\$d = +22;\n"),
            array("<?php\n\$d=-22;", "<?php\n\$d = -22;\n"),
            array("<?php\n\$d=+\$plus;", "<?php\n\$d = +\$plus;\n"),
            array("<?php\n\$d= \$a==\$b;", "<?php\n\$d = \$a == \$b;\n"),
            array("<?php\n\$d= \$a!=\$b;", "<?php\n\$d = \$a != \$b;\n"),
            array("<?php\n\$d= \$a===\$b;", "<?php\n\$d = \$a === \$b;\n"),
            array("<?php\n\$d= \$a!==\$b;", "<?php\n\$d = \$a !== \$b;\n"),
            array("<?php\nif (!\$d) {\$a!==\$b;}", "<?php\nif (!\$d) {\n    \$a !== \$b;\n}\n"),
            array("<?php\nif (!\$d&&\$c) {\$a!==\$b;}", "<?php\nif (!\$d && \$c) {\n    \$a !== \$b;\n}\n"),
            array("<?php\nif (!\$d||\$c) {\$a!==\$b;}", "<?php\nif (!\$d || \$c) {\n    \$a !== \$b;\n}\n"),
            array("<?php\n\$a=\$x ? 'a':'b';", "<?php\n\$a = \$x ? 'a' : 'b';\n"),
            array("<?php\n\$a=\$x ?:'b';", "<?php\n\$a = \$x ?: 'b';\n"),
            array("<?php\n\$a=\$x ? :'b';", "<?php\n\$a = \$x ?: 'b';\n"),
            array("<?php\n\$a=\$x==5 ? 'a':'b';", "<?php\n\$a = \$x == 5 ? 'a' : 'b';\n"),
            array("<?php\n\$a=(\$x==5)? 'a':'b';", "<?php\n\$a = \$x == 5 ? 'a' : 'b';\n"),
            array("<?php\n\$a=(\$x==5)? 'a':(\$y ? 'a' : 'b');", "<?php\n\$a = \$x == 5 ? 'a' : (\$y ? 'a' : 'b');\n"),
            array("<?php\nif (!\$d>\$c) {\$a!==\$b;}", "<?php\nif (!\$d > \$c) {\n    \$a !== \$b;\n}\n"),
            array("<?php\nif (!\$d>=\$c) {\$a!==\$b;}", "<?php\nif (!\$d >= \$c) {\n    \$a !== \$b;\n}\n"),
            array("<?php\nif (!\$d<\$c) {\$a!==\$b;}", "<?php\nif (!\$d < \$c) {\n    \$a !== \$b;\n}\n"),
            array("<?php\nif (!\$d<=\$c) {\$a!==\$b;}", "<?php\nif (!\$d <= \$c) {\n    \$a !== \$b;\n}\n"),
            array(
                "<?php\nif(\$d  instanceof  MyClass){\$d=null;}",
                "<?php\nif (\$d instanceof MyClass) {" . "\n    \$d = null;\n}\n"
            ),
            array(
                "<?php\nclass Zoo {function zoo() {\$alligator = (\$bear !== \$cat && \$dragon > \$elephant && " .
                "\$fox->isSlick()) ? 'a' : 'b';}}",
                "<?php\nclass Zoo\n{" .
                "\n    public function zoo()" .
                "\n    {" .
                "\n        \$alligator = \$bear !== \$cat && \$dragon > \$elephant && \$fox->isSlick() ? 'a' : 'b';" .
                "\n    }\n}\n"
            ),
            array(
                "<?php\nclass Zoo {function zoo() {\$alligator = ((\$bear !== \$cat || \$dragon) > \$elephant && " .
                "\$fox->isSlick()) ? 'a' : 'b';}}",
                "<?php\nclass Zoo\n{" .
                "\n    public function zoo()" .
                "\n    {" .
                "\n        \$alligator = (\$bear !== \$cat || \$dragon) > \$elephant && \$fox->isSlick() ? 'a' : 'b';" .
                "\n    }\n}\n"
            ),
            array(
                "<?php\nclass Zoo {function zoo() {\$alligator = ((\$bear !== \$cat || \$dragon) > \$elephant && " .
                "\$fox->isSlick()) ? 'a' : (((\$bear !== \$cat || \$dragon) > \$elephant && " .
                "\$fox->isSlick()) ? 'x' : 'y');}}",
                "<?php\nclass Zoo\n{" .
                "\n    public function zoo()" .
                "\n    {" .
                "\n        \$alligator = (\$bear !== \$cat ||" .
                "\n            \$dragon) > \$elephant && \$fox->isSlick() ? 'a' : ((\$bear !== \$cat ||" .
                "\n            \$dragon) > \$elephant && \$fox->isSlick() ? 'x' : 'y');" .
                "\n    }\n}\n"
            ),
            array(
                "<?php\nclass Zoo {function zoo() {\$zooAnimals=\$alligator+\$bear-\$cat*\$dragon/\$elephant^\$fox&" .
                "\$giraffe+\$hippopotamus+\$iguana+\$jackle;}}",
                "<?php\nclass Zoo\n{\n    public function zoo()\n    {" .
                "\n        \$zooAnimals = \$alligator + \$bear - \$cat * \$dragon / \$elephant ^" .
                "\n            \$fox & \$giraffe + \$hippopotamus + \$iguana + \$jackle;" .
                "\n    }\n}\n"
            ),
            array(
                "<?php\nclass Zoo {function zoo() {if (\$alligator&&\$bear||\$cat&&!\$dragon||\$elephant and \$fox or" .
                "\$giraffe xor \$hippopotamus && \$iguana && !\$jackle) { \$x += \$y;\necho 'hi';}}}",
                "<?php\nclass Zoo\n{\n    public function zoo()" .
                "\n    {" .
                "\n        if (\$alligator && \$bear || \$cat && !\$dragon || \$elephant and \$fox or" .
                "\n            \$giraffe xor \$hippopotamus && \$iguana && !\$jackle" .
                "\n        ) {" .
                "\n            \$x += \$y;" .
                "\n            echo 'hi';" .
                "\n        }" .
                "\n    }\n}\n"
            )
        );
    }
}
