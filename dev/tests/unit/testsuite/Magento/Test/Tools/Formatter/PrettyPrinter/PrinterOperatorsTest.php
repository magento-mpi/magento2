<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgedeon
 * Date: 10/23/13
 * Time: 9:21 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Magento\Test\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Printer;

class PrinterOperatorsTest extends TestBase
{
    /**
     * This method tests arrays in the pretty printer.
     *
     * @dataProvider dataOperators
     */
    public function testOperators($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
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
            array("<?php\n\$d=1+1;", "<?php\n\$d = 1 + 1;\n"),
            array("<?php\n\$d=1*1;", "<?php\n\$d = 1 * 1;\n"),
            array("<?php\n\$d=1/1;", "<?php\n\$d = 1 / 1;\n"),
            array("<?php\n\$d=1-1;", "<?php\n\$d = 1 - 1;\n"),
            array("<?php\n\$d=1/1*2+1;", "<?php\n\$d = 1 / 1 * 2 + 1;\n"),
            array("<?php\n\$d=1/1*(2+1);", "<?php\n\$d = 1 / 1 * (2 + 1);\n"),
            array("<?php\n\$d=&\$refable;", "<?php\n\$d =& \$refable;\n"),
            array("<?php\n\$d+=2-3-4*(4+6);", "<?php\n\$d += 2 - 3 - 4 * (4 + 6);\n"),
            array("<?php\n\$d-=2-3-4*(4+6);", "<?php\n\$d -= 2 - 3 - 4 * (4 + 6);\n"),
            array("<?php\n\$d*=2-3-4*(4+6);", "<?php\n\$d *= 2 - 3 - 4 * (4 + 6);\n"),
            array("<?php\n\$d/=2-3-4*(4+6);", "<?php\n\$d /= 2 - 3 - 4 * (4 + 6);\n"),
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
            array(
                "<?php\n\$d =\$ham or (\$eggs and \$a) xor \$b;",
                "<?php\n\$d = \$ham or \$eggs and \$a xor \$b;\n"
            ),
            array(
                "<?php\n\$d =(\$ham or \$eggs) and \$a xor \$b;",
                "<?php\n\$d = (\$ham or \$eggs) and \$a xor \$b;\n"
            ),
            array("<?php\n\$d=~\$a;", "<?php\n\$d = ~\$a;\n"),
            array("<?php\n\$d=++\$a;", "<?php\n\$d = ++\$a;\n"),
            array("<?php\n\$d=22+ ++\$a;", "<?php\n\$d = 22 + ++\$a;\n"),
            array("<?php\n\$d=--\$a;", "<?php\n\$d = --\$a;\n"),
            array("<?php\n\$d=22+ --\$a;", "<?php\n\$d = 22 + --\$a;\n"),
            array("<?php\n\$d=\$a++;", "<?php\n\$d = \$a++;\n"),
            array("<?php\n\$d=22+ \$a++;", "<?php\n\$d = 22 + \$a++;\n"),
            array("<?php\n\$d=\$a--;", "<?php\n\$d = \$a--;\n"),
            array("<?php\n\$d=22+ \$a--;", "<?php\n\$d = 22 + \$a--;\n"),
            array("<?php\n\$d=+22;", "<?php\n\$d = +22;\n"),
            array("<?php\n\$d=+\$plus;", "<?php\n\$d = +\$plus;\n"),
            array("<?php\n\$d= \$a==\$b;", "<?php\n\$d = \$a == \$b;\n"),
            array("<?php\n\$d= \$a!=\$b;", "<?php\n\$d = \$a != \$b;\n"),
            array("<?php\n\$d= \$a===\$b;", "<?php\n\$d = \$a === \$b;\n"),
            array("<?php\n\$d= \$a!==\$b;", "<?php\n\$d = \$a !== \$b;\n"),
            array("<?php\nif (!\$d) {\$a!==\$b;}", "<?php\nif (!\$d) {\n    \$a !== \$b;\n}\n"),
            array("<?php\nif (!\$d&&\$c) {\$a!==\$b;}", "<?php\nif (!\$d && \$c) {\n    \$a !== \$b;\n}\n"),
            array("<?php\nif (!\$d||\$c) {\$a!==\$b;}", "<?php\nif (!\$d || \$c) {\n    \$a !== \$b;\n}\n"),
        );
    }
}
