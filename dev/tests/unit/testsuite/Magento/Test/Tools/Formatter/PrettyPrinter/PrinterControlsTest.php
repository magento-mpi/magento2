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
                "<?php class G1 {public function a(){foreach (\$as as \$k=>\$a){break 2;}}}",
                "<?php\nclass G1\n{\n    public function a()\n    {\n        foreach (\$as as \$k => \$a) {\n" .
                "            break 2;\n        }\n    }\n}\n"
            ),
            array(
                "<?php class G2 {public function a(){foreach (\$as as \$k=>\$a){continue 22;}}}",
                "<?php\nclass G2\n{\n    public function a()\n    {\n        foreach (\$as as \$k => \$a) {\n" .
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
            )
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
        $originalIf5 = <<<'ORIGINALIF5'
<?php class If5 {
protected function alpha() {
        if($response->getResultCode() == self::RESPONSE_CODE_VOID_ERROR) {
            throw new \Magento\SomeModule\Exception(__('You cannot void a verification transaction.'));
        }elseif($response->getResultCode() != self::RESPONSE_CODE_APPROVED
            && $response->getResultCode() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER
        ){throw new \Magento\Framework\Model\Exception($response->getRespmsg());}}}
ORIGINALIF5;
        $formattedIf5 = <<<'FORMATTEDIF5'
<?php
class If5
{
    protected function alpha()
    {
        if ($response->getResultCode() == self::RESPONSE_CODE_VOID_ERROR) {
            throw new \Magento\SomeModule\Exception(__('You cannot void a verification transaction.'));
        } elseif ($response->getResultCode() != self::RESPONSE_CODE_APPROVED &&
            $response->getResultCode() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER
        ) {
            throw new \Magento\Framework\Model\Exception($response->getRespmsg());
        }
    }
}

FORMATTEDIF5;
        $originalIf6 = <<<'ORIGINALIF6'
<?php class If6 {
public function if6() {
    if ($this->_request->getActionName() === $action &&
        (null === $module || $this->_request->getModuleName() === $module)
        && (null === $controller || $this->_request->getControllerName() === $controller)
    )
    {return;}}}
ORIGINALIF6;
        $formattedIf6 = <<<'FORMATTEDIF6'
<?php
class If6
{
    public function if6()
    {
        if ($this->_request->getActionName() === $action && (null === $module ||
            $this->_request->getModuleName() === $module) && (null === $controller ||
            $this->_request->getControllerName() === $controller)
        ) {
            return;
        }
    }
}

FORMATTEDIF6;

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
            array($originalIf5, $formattedIf5),
            array($originalIf6, $formattedIf6)
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
        $originalCodeSnippet = <<<'ORIGINALCODESNIPPET'
<?php
namespace Magento\Tools\Formatter\TestClass;
class TestClass {
    public function main($abcdefghijklmnopqrstuvwxyz) {
        if (isset($abcdefghijklmnopqrstuvwxyz)&&isset($abcdefghijklmnopqrstuvwxyz)&&
            isset($abcdefghijklmnopqrstuvwxyz)) {
            $callback = 'hello';
            $callback = 'good';
            $callback = 'bye';
            if (isset($abcdefghijklmnopqrstuvwxyz)) {
                $callback = 'asdf';
            }}}}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet = <<<'FORMATTEDCODESNIPPET'
<?php
namespace Magento\Tools\Formatter\TestClass;

class TestClass
{
    public function main($abcdefghijklmnopqrstuvwxyz)
    {
        if (isset(
            $abcdefghijklmnopqrstuvwxyz
        ) && isset(
            $abcdefghijklmnopqrstuvwxyz
        ) && isset(
            $abcdefghijklmnopqrstuvwxyz
        )
        ) {
            $callback = 'hello';
            $callback = 'good';
            $callback = 'bye';
            if (isset($abcdefghijklmnopqrstuvwxyz)) {
                $callback = 'asdf';
            }
        }
    }
}

FORMATTEDCODESNIPPET;
        $originalCodeSnippet2 = <<<'ORIGINALCODESNIPPET'
<?php
namespace Magento\Tools\Formatter\TestClass;
class TestClass {
    public function main($results) {
        if (strcasecmp('FALSE', $results) === 0 || strcasecmp('TRUE', $results) === 0 ||
            strcasecmp('NULL', $results) === 0) {
            $tokens[sizeof($tokens) - 1] = strtolower($results);
            // reset the last item in the array due to php's "copy-on-write" rule for arrays
            $treeNode->getData()->line->setTokens($tokens);
        }
    }
}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet2 = <<<'FORMATTEDCODESNIPPET'
<?php
namespace Magento\Tools\Formatter\TestClass;

class TestClass
{
    public function main($results)
    {
        if (strcasecmp(
            'FALSE',
            $results
        ) === 0 || strcasecmp(
            'TRUE',
            $results
        ) === 0 || strcasecmp(
            'NULL',
            $results
        ) === 0
        ) {
            $tokens[sizeof($tokens) - 1] = strtolower($results);
            // reset the last item in the array due to php's "copy-on-write" rule for arrays
            $treeNode->getData()->line->setTokens($tokens);
        }
    }
}

FORMATTEDCODESNIPPET;
        $originalCodeSnippet3 = <<<'ORIGINALCODESNIPPET'
<?php
namespace Magento\Tools\Formatter\TestClass;
class TestArrayParameter {
    public function main($results) {
        $element->setDisabled(array(\Magento\Catalog\Model\Session::DISPLAY_CATEGORY_PAGE,
                \Magento\Catalog\Model\Session::DISPLAY_PRODUCT_PAGE));
    }
}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet3 = <<<'FORMATTEDCODESNIPPET'
<?php
namespace Magento\Tools\Formatter\TestClass;

class TestArrayParameter
{
    public function main($results)
    {
        $element->setDisabled(
            array(
                \Magento\Catalog\Model\Session::DISPLAY_CATEGORY_PAGE,
                \Magento\Catalog\Model\Session::DISPLAY_PRODUCT_PAGE
            )
        );
    }
}

FORMATTEDCODESNIPPET;
        $originalCodeSnippet4 = <<<'ORIGINALCODESNIPPET'
<?php
class TestIfCase{
    public function main($results) {
        if ($otherCode) {
            $files = array_merge($files,glob($this->_path . '/*.php', GLOB_NOSORT),
                glob($this->_path . '/pub/*.php', GLOB_NOSORT),
                self::getFiles(array("{$this->_path}/downloader"), '*.php'),
                self::getFiles(array("{$this->_path}/lib/internal/{Mage,Magento,Varien}"), '*.php')
            );
        }}}
ORIGINALCODESNIPPET;
        $formattedCodeSnippet4 = <<<'FORMATTEDCODESNIPPET'
<?php
class TestIfCase
{
    public function main($results)
    {
        if ($otherCode) {
            $files = array_merge(
                $files,
                glob($this->_path . '/*.php', GLOB_NOSORT),
                glob($this->_path . '/pub/*.php', GLOB_NOSORT),
                self::getFiles(array("{$this->_path}/downloader"), '*.php'),
                self::getFiles(array("{$this->_path}/lib/internal/{Mage,Magento,Varien}"), '*.php')
            );
        }
    }
}

FORMATTEDCODESNIPPET;
        $originalClosure = <<<'ORIGINALCLOSURE'
<?php
class CSample {
public function cS() {
$trimFunction = function (&$value) {
    $value = trim($value, PHP_EOL . ' $');
};
}}
ORIGINALCLOSURE;
        $formattedClosure = <<<'FORMATTEDCLOSURE'
<?php
class CSample
{
    public function cS()
    {
        $trimFunction = function (&$value) {
            $value = trim($value, PHP_EOL . ' $');
        };
    }
}

FORMATTEDCLOSURE;
        $originalClosure2 = <<<'ORIGINALCLOSURE2'
<?php
class CSample2 {
    public function cS2() {
        $assignedThemeIds = array_map(
            function ($theme) {
                return $theme->getId();
            },
            $this->_customizationConfig->getAssignedThemeCustomizations()
        );}}
ORIGINALCLOSURE2;
        $formattedClosure2 = <<<'FORMATTEDCLOSURE2'
<?php
class CSample2
{
    public function cS2()
    {
        $assignedThemeIds = array_map(
            function ($theme) {
                return $theme->getId();
            },
            $this->_customizationConfig->getAssignedThemeCustomizations()
        );
    }
}

FORMATTEDCLOSURE2;
        $originalClosure3 = <<<'ORIGINALCLOSURE3'
<?php
class CSample3 {
    public function cS3() {
        $order = array_merge(array($codeDir, $jsDir), array_map(function ($fileTheme) {
            /** @var $fileTheme \Magento\Framework\View\Design\ThemeInterface */
            return $fileTheme->getThemeId();
        }, $themes));}}
ORIGINALCLOSURE3;
        $formattedClosure3 = <<<'FORMATTEDCLOSURE3'
<?php
class CSample3
{
    public function cS3()
    {
        $order = array_merge(
            array($codeDir, $jsDir),
            array_map(
                function ($fileTheme) {
                    /** @var $fileTheme \Magento\Framework\View\Design\ThemeInterface */
                    return $fileTheme->getThemeId();
                },
                $themes
            )
        );
    }
}

FORMATTEDCLOSURE3;
        $originalClosure4 = <<<'ORIGINALCLOSURE4'
<?php
class CSample4 {
    public function cS4() {
        $this->redis->pipeline(function($pipe) use($keys, $me) {
            foreach ($keys as $k) {
                $pipe->hdel($me->getKey(), $k);
            }
        });}}
ORIGINALCLOSURE4;
        $formattedClosure4 = <<<'FORMATTEDCLOSURE4'
<?php
class CSample4
{
    public function cS4()
    {
        $this->redis->pipeline(
            function ($pipe) use ($keys, $me) {
                foreach ($keys as $k) {
                    $pipe->hdel($me->getKey(), $k);
                }
            }
        );
    }
}

FORMATTEDCLOSURE4;
        $originalClosure5 = <<<'OC5'
<?php
class CSample5 {
    public function cS5() {
        $this->_block
            ->expects($this->any())
            ->method('_getLayoutProcessor')
            ->will($this->returnCallback(
                function () use ($layoutUtility) {
                    return $layoutUtility->getLayoutUpdateFromFixture(glob(__DIR__ . '/_files/layout/*.xml'));
                }
            ))
        ;}}
OC5;
        $formattedClosure5 = <<<'FC5'
<?php
class CSample5
{
    public function cS5()
    {
        $this->_block->expects($this->any())->method('_getLayoutProcessor')->will(
            $this->returnCallback(
                function () use ($layoutUtility) {
                    return $layoutUtility->getLayoutUpdateFromFixture(glob(__DIR__ . '/_files/layout/*.xml'));
                }
            )
        );
    }
}

FC5;
        $originalClosure6 = <<<'OC6'
<?php
class CSample6 {
    public function cS6() {
        $pattern = array('id' => '%s','name' => 'Static',
            'calculated' => function ($index) {
                return $index * 10;
            },
        );}}
OC6;
        $formattedClosure6 = <<<'FC6'
<?php
class CSample6
{
    public function cS6()
    {
        $pattern = array(
            'id' => '%s',
            'name' => 'Static',
            'calculated' => function ($index) {
                return $index * 10;
            }
        );
    }
}

FC6;
        $originalClosure7 = <<<'OC7'
<?php
class CSample7 {
    public function cS7() {
        $option = new \Magento\Framework\Validator\Constraint\Option\Callback(
            function () {
            }
        );
}}
OC7;
        $formattedClosure7 = <<<'FC7'
<?php
class CSample7
{
    public function cS7()
    {
        $option = new \Magento\Framework\Validator\Constraint\Option\Callback(
            function () {
            }
        );
    }
}

FC7;
        $originalMethodCall = <<<'OMC'
<?php
class MC1 {
    public function mC1() {
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$creditmemo->getGwItemsBasePrice()
            +$creditmemo->getGwBasePrice()+$creditmemo->getGwCardBasePrice());}}
OMC;
        $formattedMethodCall = <<<'FMC'
<?php
class MC1
{
    public function mC1()
    {
        $creditmemo->setBaseGrandTotal(
            $creditmemo->getBaseGrandTotal() +
            $creditmemo->getGwItemsBasePrice() +
            $creditmemo->getGwBasePrice() +
            $creditmemo->getGwCardBasePrice()
        );
    }
}

FMC;
        $originalMethodCall2 = <<<'OMC2'
<?php
class MC2 {
    public function mC2() {
        if (
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$creditmemo->getGwItemsBasePrice()
            +$creditmemo->getGwBasePrice()+$creditmemo->getGwCardBasePrice())){echo 'hi';}}}
OMC2;
        $formattedMethodCall2 = <<<'FMC2'
<?php
class MC2
{
    public function mC2()
    {
        if ($creditmemo->setBaseGrandTotal(
            $creditmemo->getBaseGrandTotal() +
            $creditmemo->getGwItemsBasePrice() +
            $creditmemo->getGwBasePrice() +
            $creditmemo->getGwCardBasePrice()
        )
        ) {
            echo 'hi';
        }
    }
}

FMC2;
        $originalMethodCall3 = <<<'OMC3'
<?php
class MC3 { public function mC3(){
        // redirect to first allowed website or store scope
        if ($this->_role->getWebsiteIds()) {
            return $this->_redirect($controller, $this->_backendUrl->getUrl(
                    'adminhtml/system_config/edit',
                    array('website' => $this->_storeManager->getDefaultStoreView()->getWebsite()->getCode())
                ));}
        $this->_redirect($controller, $this->_backendUrl->getUrl('adminhtml/system_config/edit', array(
                    'website' => $this->_storeManager->getDefaultStoreView()->getWebsite()->getCode(),
                    'store' => $this->_storeManager->getDefaultStoreView()->getCode())
                ));}}
OMC3;
        $formattedMethodCall3 = <<<'FMC3'
<?php
class MC3
{
    public function mC3()
    {
        // redirect to first allowed website or store scope
        if ($this->_role->getWebsiteIds()) {
            return $this->_redirect(
                $controller,
                $this->_backendUrl->getUrl(
                    'adminhtml/system_config/edit',
                    array('website' => $this->_storeManager->getDefaultStoreView()->getWebsite()->getCode())
                )
            );
        }
        $this->_redirect(
            $controller,
            $this->_backendUrl->getUrl(
                'adminhtml/system_config/edit',
                array(
                    'website' => $this->_storeManager->getDefaultStoreView()->getWebsite()->getCode(),
                    'store' => $this->_storeManager->getDefaultStoreView()->getCode()
                )
            )
        );
    }
}

FMC3;
        $originalMethodCall4 = <<<'OMC4'
<?php
class MC4 { public function mC4(){
        $select->where("{$inversion} (" . "quote.coupon_code IS NOT NULL AND quote.coupon_code <> "
            . $select->getAdapter()->quote('') . ")");}}
OMC4;
        $formattedMethodCall4 = <<<'FMC4'
<?php
class MC4
{
    public function mC4()
    {
        $select->where(
            "{$inversion} (" .
            "quote.coupon_code IS NOT NULL AND quote.coupon_code <> " .
            $select->getAdapter()->quote(
                ''
            ) . ")"
        );
    }
}

FMC4;
        $originalSwitch = <<<'OS0'
<?php class OS0 { public function oS0(){
switch($this->option) {
// case 1 comment
case 1: break;
// case 2 comment
case 2:break;
// default comment
default:break;}
}}
OS0;
        $formattedSwitch = <<<'FS0'
<?php
class OS0
{
    public function oS0()
    {
        switch ($this->option) {
            // case 1 comment
            case 1:
                break;
                // case 2 comment
            case 2:
                break;
                // default comment
            default:
                break;
        }
    }
}

FS0;

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
            array($originalClosure, $formattedClosure),
            array($originalClosure2, $formattedClosure2),
            array($originalClosure3, $formattedClosure3),
            array($originalClosure4, $formattedClosure4),
            array($originalClosure5, $formattedClosure5),
            array($originalClosure6, $formattedClosure6),
            array($originalClosure7, $formattedClosure7),
            array($originalMethodCall, $formattedMethodCall),
            array($originalMethodCall2, $formattedMethodCall2),
            array($originalMethodCall3, $formattedMethodCall3),
            array($originalMethodCall4, $formattedMethodCall4),
            array($originalSwitch, $formattedSwitch)
        );
    }
}
