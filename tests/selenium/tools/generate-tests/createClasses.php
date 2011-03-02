<?php

include 'tests.php';

/** @var $content string */
$baseTemplate = file_get_contents('templates/stub.php');

$baseMethodTemplate = preg_replace('/.*?\/\/startMethodBlock(.*?)\/\/endMethodBlock.*/si', '$1', $baseTemplate);
$baseClassTemplate = preg_replace('/\/\/startMethodBlock(.*?)\/\/endMethodBlock/si', '{method}', $baseTemplate);

$mergedTests = array();

if (!empty($tests) && is_array($tests)){

    foreach ($tests as $test) {
        if (isset($test['path'], $test['file'], $test['class'], $test['method'])) {
            $dir    = '../tests/' . str_replace('\\', '/', trim($test['path']));
            $file   = trim($test['file']);
            $class  = trim($test['class']);
            $method = trim($test['method']);
            $method = preg_replace("/[^\w\_]/", '', $method);

            if($dir && $file && $class && $method){
                if (!isset($mergedTests[$class])) {
                    $mergedTests[$class] = array(
                        'dir'=>$dir,
                        'file'=>$file,
                        'class'=>$class,
                        'method'=>array($method),
                    );

                } else {
                    $mergedTests[$class]['method'][] = $method;
                }
            }
        }

    }
}

if (!empty($mergedTests) && is_array($mergedTests)){

    foreach ($mergedTests as $test) {


            $dir    = $test['dir'];
            $file   = $test['file'];
            $class  = $test['class'];
            $methods = (array) $test['method'];

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $classContent  = str_replace('CLASSNAME', $class, $baseClassTemplate);
            $methodContent = '';

            foreach($methods as $method){

                $methodContent .= str_replace('METHODNAME', $method, $baseMethodTemplate);
            }

            $content = str_replace('{method}', $methodContent, $classContent);

            file_put_contents($dir . '/' . $file, $content);
        
    }
}
