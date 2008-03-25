<?php

require_once "lib/Varien/Pear.php";

$pear = Varien_Pear::getInstance();

#$pear->getConfig()->set('remote_config', 'ftp://moshe:wiodohfe@var-dev/home/moshe/dev/magento_pear/lib/pear/rw/pear.ini');

echo '<p><a href="?command=list-categories">List all categories</a>';
echo ' | <a href="?command=list-all">List all available packages</a></p>';
echo '<form method="GET">Search for a package: <input type="hidden" name="command" value="search"><input name="query"><input type="submit" value="Go"></form>';

if (!empty($_GET['command'])) {
    $command = $_GET['command'];
    $package = !empty($_GET['package']) ? $_GET['package'] : null;
    $options = array();
    if (!empty($_GET['channel'])) {
        $options['channel'] = $_GET['channel'];
    }
    switch ($command) {
        case 'uninstall':
            if ($pear->isSystemPackage($package)) {
                echo "Can not uninstall system packages";
                break;
            }
        case 'install':
        case 'upgrade':
            if ($command=='install') {
                $options['onlyreqdeps'] = 1;
            }
            $result = $pear->run($command, $options, array($package));

            if ($pear->getLog()) {
                echo "<pre>".print_r($pear->getLog(),1)."</pre>";
            }

            if ($result instanceof PEAR_Error) {
                #echo "<pre>".print_r($result,1)."</pre>";
                echo '<p>'.$result->message.'</p>';
                break;
            }
            $output = $pear->getOutput();
            $data = $output[0]['output'];
            if (!is_array($data)) {
                echo "<p>".$data."</p>";
                break;
            }
            echo "<p>".$data['data']."</p>";
            break;

        case 'list-categories':
            $result = $pear->run($command, $options, array());
            if ($result instanceof PEAR_Error) {
                echo '<p>'.$result->message.'</p>';
                break;
            }
            $output = $pear->getOutput();
            $data = $output[0]['output'];
            if (!is_array($data)) {
                echo "<p>".$data."</p>";
                break;
            }
            echo '<h2>All Categories</h2><table border="1"><thead><tr>';
            foreach ($data['headline'] as $label) {
                echo '<th>'.$label.'</th>';
            }
            echo '<th>Actions</th>';
            echo '</tr></thead><tbody>';
            foreach ($data['data'] as $row) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>'.$value.'</td>';
                }
                echo '<td><a href="?command=list-category&channel='.urlencode($row[0]).'&category='.urlencode($row[1]).'">List Packages</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            break;

        case 'list-category':
            $result = $pear->run($command, $options, array($_GET['category']));
            if ($result instanceof PEAR_Error) {
                echo '<p>'.$result->message.'</p>';
                break;
            }
            $output = $pear->getOutput();
            $data = $output[0]['output'];
            if (!is_array($data)) {
                echo "<p>".$data."</p>";
                break;
            }
            echo '<h2>Packages in '.$_GET['channel'].'/'.$_GET['category'].'</h2>';
            if (!is_array($data['data'])) {
                echo "<p>".$data['data']."</p>";
                break;
            }
            echo '<table border="1"><thead><tr>';
            foreach ($data['headline'] as $label) {
                echo '<th>'.$label.'</th>';
            }
            echo '<th>Actions</th>';
            echo '</tr></thead><tbody>';
            foreach ($data['data'] as $row) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>'.$value.'</td>';
                }
                echo '<td>';
                echo '<a href="?command=info&channel='.urlencode($row[0]).'&package='.urlencode($row[1]).'">Info</a>';
                if ($row[2]=='-') {
                    echo ' | <a href="?command=install&channel='.urlencode($row[0]).'&package='.urlencode($row[1]).'">Install</a>';
                } else {
                    if (!$pear->isSystemPackage($package)) {
                        echo ' | <a href="?command=uninstall&channel='.urlencode($row[0]).'&package='.urlencode($row[1]).'">Uninstall</a>';
                    }
                    if (version_compare($row[2], $row[1], '>')===true) {
                        echo ' | <a href="?command=upgrade&channel='.urlencode($row[0]).'&package='.urlencode($row[1]).'">Upgrade</a>';
                    }
                }
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
            break;

        case 'list-all':
            $result = $pear->run($command, $options, array());
            if ($result instanceof PEAR_Error) {
                echo '<p>'.$result->message.'</p>';
                break;
            }
            $output = $pear->getOutput();
            $data = $output[0]['output'];
            if (!is_array($data)) {
                echo '<p>'.$data.'</p>';
                break;
            }
            echo '<h2>All the packages by category</h2>';
            if (!is_array($data['data'])) {
                echo "<p>".$data['data']."</p>";
                break;
            }
            echo '<table border="1"><thead><tr>';
            foreach ($data['headline'] as $label) {
                echo '<th>'.$label.'</th>';
            }
            echo '<th>Description</th><th>Actions</th>';
            echo '</tr></thead><tbody>';
            foreach ($data['data'] as $categoryName=>$packages) {
                echo '<tr><th colspan="10" style="background:#CCC;">'.$categoryName.'</th></tr>';
                foreach ($packages as $row) {
                    echo '<tr>';
                    #echo '<td>'; print_r($row); echo '</td></tr>'; continue;
                    foreach ($row as $value) {
                        if (is_array($value)) {
                            continue;
                        }
                        echo '<td>'.(!empty($value) ? $value : '&nbsp;').'</td>';
                    }
                    echo '<td>';
                    echo '<a href="?command=info&package='.urlencode($row[0]).'">Info</a>';
                    if ($row[2]=='') {
                        echo ' | <a href="?command=install&package='.urlencode($row[0]).'">Install</a>';
                    } else {
                        $pkgArr = explode('/', $row[0]);
                        if (!$pear->isSystemPackage($pkgArr[1])) {
                            echo ' | <a href="?command=uninstall&package='.urlencode($row[0]).'">Uninstall</a>';
                        }
                        if (version_compare($row[2], $row[3], '>')===true) {
                            echo ' | <a href="?command=upgrade&package='.urlencode($row[0]).'">Upgrade</a>';
                        }
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            }

            echo '</tbody></table>';
            break;

        case 'search':
            $result = $pear->run($command, $options, array($_GET['query']));
            if ($result instanceof PEAR_Error) {
                echo '<p>'.$result->message.'</p>';
                break;
            }
            $output = $pear->getOutput();
            $data = $output[0]['output'];
            echo '<h2>Packages matching &quot;'.$_GET['query'].'&quot;</h2>';
            if (!is_array($data['data'])) {
                echo "<p>".$data['data']."</p>";
                break;
            }
            echo '<table border="1"><thead><tr>';
            foreach ($data['headline'] as $label) {
                echo '<th>'.$label.'</th>';
            }
            echo '<th>Description</th><th>Actions</th>';
            echo '</tr></thead><tbody>';
            foreach ($data['data'] as $categoryName=>$packages) {
                echo '<tr><th colspan="10" style="background:#CCC;">'.$categoryName.'</th></tr>';
                foreach ($packages as $row) {
                    echo '<tr>';
                    #echo '<td>'; print_r($row); echo '</td></tr>'; continue;
                    foreach ($row as $value) {
                        if (is_array($value)) {
                            continue;
                        }
                        echo '<td>'.(!empty($value) ? $value : '&nbsp;').'</td>';
                    }
                    echo '<td>';
                    echo '<a href="?command=info&channel='.urlencode($data['channel']).'&package='.urlencode($row[0]).'">Info</a>';
                    if ($row[2]=='') {
                        echo '| <a href="?command=install&channel='.urlencode($data['channel']).'&package='.urlencode($row[0]).'">Install</a>';
                    } else {
                        if (!$pear->isSystemPackage($row[0])) {
                            echo '| <a href="?command=uninstall&channel='.urlencode($data['channel']).'&package='.urlencode($row[0]).'">Uninstall</a>';
                        }
                        if (version_compare($row[2], $row[3], '>')===true) {
                            echo ' | <a href="?command=upgrade&channel='.urlencode($data['channel']).'&package='.urlencode($row[0]).'">Upgrade</a>';
                        }
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            }

            echo '</tbody></table>';
            break;

        case 'info':
            $result = $pear->run('remote-info', $options, array($package));
            if ($result instanceof PEAR_Error) {
                echo '<p>'.$result->message.'</p>';
                break;
            }
            $output = $pear->getOutput();
            $data = $output[0]['output'];
            echo '<h2>'.$data['name'].'</h2>';
            echo '<h4>'.$data['summary'].'</h4>';
            echo '<pre>'.$data['description'].'</pre>';
            foreach ($data['releases'] as $version=>$release) {
                echo '<h4>Release: '.$version.' ('.$release['state'].') - '.$release['releasedate'].'</h4>';
                echo '<pre>Notes: '.$release['releasenotes'].'</pre>';
                if (!empty($release['deps'])) {
                    echo '<p>Dependencies: <ul>';
                    foreach ($release['deps'] as $dep) {
                        if ($dep['type']!=='pkg') {
                            continue;
                        }
                        echo '<li>'.$dep['name'].(!empty($dep['optional']) && $dep['optional']==='yes' ? ' (optional)' : '').'</li>';
                    }
                    echo '</ul></p>';
                }
            }
            break;
    }
}

//////////////////////// SHOW INSTALLED PACKAGES
$pear->getFrontend()->clear();
$result = $pear->run('list', array(), array());
if ($result instanceof PEAR_Error) {
    echo '<p>'.$result->message.'</p>';
    break;
}
$output = $pear->getOutput();
$data = $output[0]['output'];
echo '<h2>Installed Packages</h2><table border="1"><thead><tr>';
foreach ($data['headline'] as $label) {
    echo '<th>'.$label.'</th>';
}
echo '<th>Action</th>';
echo '</tr></thead><tbody>';
foreach ($data['data'] as $row) {
    echo '<tr>';
    foreach ($row as $value) {
        echo '<td>'.$value.'</td>';
    }
    echo '<td>';
    echo '<a href="?command=info&package='.$row[0].'">Info</a>';
    echo ' | <a href="?command=upgrade&package='.$row[0].'">Upgrade</a>';
    if (!$pear->isSystemPackage($row[0])) {
        echo ' | <a href="?command=uninstall&package='.$row[0].'">Uninstall</a>';
    }
    echo '</td></tr>';
}

echo '</tbody></table>';

