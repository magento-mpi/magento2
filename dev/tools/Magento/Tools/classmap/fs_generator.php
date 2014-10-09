<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$path = false;
if (isset($argv[1])) {
    if (realpath($argv[1])) {
        $path = realpath($argv[1]);
    } elseif (realpath(getcwd() . '/' . $argv[1])) {
        $path = realpath(getcwd() . '/' . $argv[1]);
    }
}

if (!$path) {
    echo "Please specify directory for scan: php -f fs_generator.php path/to/code";
    exit;
}


$basePath = realpath(__DIR__ . '/../../../') . '/';
$directory = new RecursiveDirectoryIterator($path);
$iterator = new RecursiveIteratorIterator($directory);
$regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);


$map = array();
foreach ($regex as $file) {
    $filePath = str_replace('\\', '/', str_replace($basePath, '', $file[0]));
    if (strpos($filePath, 'dev') === 0 || strpos($filePath, 'shell') === 0) {
        continue;
    }

    $code = file_get_contents($file[0]);
    $tokens = token_get_all($code);

    $count = count($tokens);
    $i = 0;
    $namespace = '';
    while ($i < $count) {
        $token = $tokens[$i];

        if (!is_array($token)) {
            $i++;
            continue;
        }

        list($id, $content, $line) = $token;

        switch ($id) {
            case T_NAMESPACE:
                if (!empty($namespace)) {
                    throw new \Exception('Namespace declared more that once in ' . $file[0]);
                }
                do {
                    ++$i;
                    $token = $tokens[$i];
                    if (is_string($token)) {
                        continue;
                    }
                    list($type, $content, $line) = $token;
                    switch ($type) {
                        case T_STRING:
                        case T_NS_SEPARATOR:
                            $namespace .= $content;
                            break;
                    }
                } while ($token !== ';' && $i < $count);
                break;
            case T_CLASS:
            case T_INTERFACE:
                $class = '';
                do {
                    ++$i;
                    $token = $tokens[$i];
                    if (is_string($token)) {
                        continue;
                    }
                    list($type, $content, $line) = $token;
                    switch ($type) {
                        case T_STRING:
                            $class = $content;
                            break;
                    }
                } while (empty($class) && $i < $count);

                // If a classname was found, set it in the object, and
                // return boolean true (found)
                if (!empty($class)) {
                    $map[$namespace . '\\' . $class] = $filePath;
                }
                break;
            default:
                break;
        }
        ++$i;
    }
}

echo serialize($map);
