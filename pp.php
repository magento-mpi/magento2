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
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

function collectAllModules()
{
    $modules = explode("\n", `ls app/code/core/Mage`);
    foreach ($modules as $module) {
        if (!$module) {
            continue;
        }
        echo $module.': <hr>';
        collectModuleFiles($module);
    }
}

function collectModuleFiles($module)
{
    #$text = `cat \`find app/code/core/Mage/Core/[MBC]* -name "*.php"\``;
    $files = explode("\n", `find app/code/core/Mage/$module/[MBC]* -name "*.php"`);

    $unsorted = array();
    ob_implicit_flush();
    $cnt = sizeof($files);
    foreach ($files as $i=>$fileName) {
        if (!$fileName) {
            continue;
        }
        echo $i.'/'.$cnt.': '.$fileName."<br>";

        $text = file_get_contents($fileName)."\n";

        $text = preg_replace('#<\?php#m', '', $text);
        $text = preg_replace('#(?<=\s)/\*[^/].*?\*/#s', '', $text);
        $text = preg_replace('#^\s*(//|\#).*$#m', '', $text);
        $text = preg_replace('#^\s+#m', '', $text);

        $unsorted[] = array(
            'text'=>$text,
            'class'=>preg_match('#class\s+([a-z0-9_]+)#i', $text, $m) ? $m[1] : '',
            'interface'=>preg_match('#interface\s+([a-z0-9_]+)#i', $text, $m) ? $m[1] : '',
            'extends'=>preg_match('#extends\s+([a-z0-9_]+)#i', $text, $m) ? $m[1] : '',
            'implements'=>preg_match('#implements\s+([a-z0-9_]+)#i', $text, $m) ? $m[1] : '',
        );
    }

    echo "<hr>Sorting...";
    $sorted = array();
    foreach ($unsorted as $a) {
        foreach ($sorted as $i=>$b) {
            if ($a['class']==$b['extends'] || $a['interface'] && $a['interface']==$b['implements']) {
                array_splice($sorted, $i, 0, array($a));
                continue 2;
            }
        }
        $sorted[] = $a;
        echo 'class '.$a['class'].' interface '.$a['interface'].' extends '.$a['extends'].' implements '.$a['implements'].'<br>';
    }

    echo "<hr>Writing...";
    $text = "<"."?php\n";
    foreach ($sorted as $a) {
        $text .= $a['text'];
    }
    file_put_contents('var/cache/code/'.$module.'.php', $text);

    echo "<hr>Done!";
}

collectAllModules();