<?php
// Analyze report of theme layouts before and after refactorings
$rootDir = realpath(__DIR__ . '/../../..');

// These are results of running the layout_count tool before and after refactoring
$beforeReport = 'e:\before.txt';
$afterReport = 'e:\after.txt';

// ---Analyze---
$before = retrieve_handles_from_report($beforeReport);
$after = retrieve_handles_from_report($afterReport);

$statsBefore = get_general_stats($before);
$statsAfter = get_general_stats($after);
$statsDeleted = get_stats_deleted_handles($before, $after);
$statsNew = get_stats_new_handles($before, $after);
$statsMoved = get_stats_moved_to_own_handles($before, $after);


echo "---Before---\n";
echo "Total handles: {$statsBefore['num']}, {$statsBefore['bytes']} bytes, {$statsBefore['lines']} lines\n";
echo " - overridden handles: {$statsBefore['overridden_num']}, {$statsBefore['overridden_bytes']} bytes, {$statsBefore['overridden_lines']} lines\n";
echo " - theme own handles: {$statsBefore['own_num']}, {$statsBefore['own_bytes']} bytes, {$statsBefore['own_lines']} lines\n";
echo "\n";

echo "---After---\n";
echo "Total handles: {$statsAfter['num']}, {$statsAfter['bytes']} bytes, {$statsAfter['lines']} lines\n";
echo " - overridden handles: {$statsAfter['overridden_num']}, {$statsAfter['overridden_bytes']} bytes, {$statsAfter['overridden_lines']} lines\n";
echo " - theme own handles: {$statsAfter['own_num']}, {$statsAfter['own_bytes']} bytes, {$statsAfter['own_lines']} lines\n";
echo "\n";

echo "---Deleted handles---\n";
echo "Total handles: {$statsDeleted['num']}, {$statsDeleted['bytes']} bytes, {$statsDeleted['lines']} lines\n";
echo "\n";

echo "---New handles---\n";
echo "Total handles: {$statsNew['num']}, {$statsNew['bytes']} bytes, {$statsNew['lines']} lines\n";
echo "\n";

echo "---Moved from overridden to theme own handles---\n";
echo "Total handles: {$statsMoved['num']}\n";
echo "Stats (before -> after): {$statsMoved['bytes_before']} -> {$statsMoved['bytes_after']} bytes, {$statsMoved['lines_before']} -> {$statsMoved['lines_after']} lines\n";
echo "\n";

// ---Functions---
/**
 * Parse report file into handle info array
 *
 * @param string $file
 * @return array
 * @throws Exception
 */
function retrieve_handles_from_report($file)
{
    $result = array();
    $contents = file($file);
    $i = 0;
    while ($i < count($contents)) {
        $line = trim($contents[$i]);
        if (!strlen($line)) {
            // Per-file report finished
            break;
        }

        $handle = basename($line, '.xml');
        if (!preg_match('#/enterprise/fixed/([^/]+)/layout/#', $line, $matches)) {
            throw new Exception("Couldn't get module name for file {$line}");
        }
        $fullHandle = $matches[1] . '/' . $handle;

        $isOwn = !strpos($line, 'override');
        if (!preg_match('/Total lines: (\d+)/', $contents[$i + 1], $matches)) {
            throw new Exception("Couldn't extract total lines for file {$line}");
        }
        $totalLines = $matches[1];
        if (!preg_match('/Total bytes: (\d+)/', $contents[$i + 2], $matches)) {
            throw new Exception("Couldn't extract total bytes for file {$line}");
        }
        $totalBytes = $matches[1];

        $result[] = array(
            'handle' => $fullHandle,
            'is_own' => $isOwn,
            'lines' => $totalLines,
            'bytes' => $totalBytes,
        );

        $i+= 3;
    }
    return $result;
}

function get_general_stats($handles)
{
    $result = array(
        'num' => 0,
        'bytes' => 0,
        'lines' => 0,
        'own_num' => 0,
        'own_bytes' => 0,
        'own_lines' => 0,
        'overridden_num' => 0,
        'overridden_bytes' => 0,
        'overridden_lines' => 0,
    );

    $counted = array();
    foreach ($handles as $handleInfo) {
        $handle = $handleInfo['handle'];
        if ($handleInfo['is_own']) {
            $result['own_num']++;
            $result['own_bytes'] += $handleInfo['bytes'];
            $result['own_lines'] += $handleInfo['lines'];
        } else {
            $result['overridden_num']++;
            $result['overridden_bytes'] += $handleInfo['bytes'];
            $result['overridden_lines'] += $handleInfo['lines'];
        }
        if (!isset($counted[$handle])) {
            $result['num']++;
            $counted[$handle] = true;
        }
    }

    $result['bytes'] = $result['own_bytes'] + $result['overridden_bytes'];
    $result['lines'] = $result['own_lines'] + $result['overridden_lines'];

    return $result;
}

function get_stats_deleted_handles($before, $after)
{
    $result = array(
        'num' => 0,
        'bytes' => 0,
        'lines' => 0,
    );
    $counted = array();
    foreach ($before as $beforeInfo) {
        $handle = $beforeInfo['handle'];

        $found = false;
        foreach ($after as $afterInfo) {
            if ($afterInfo['handle'] == $handle) {
                $found = true;
                break;
            }
        }
        if ($found) {
            continue;
        }

        if (!isset($counted[$handle])) { // Count only once, when same handle is split into own and overridden
            $result['num']++;
            $counted[$handle] = true;
        }
        $result['bytes'] += $beforeInfo['bytes'];
        $result['lines'] += $beforeInfo['lines'];
    }
    return $result;
}

function get_stats_new_handles($before, $after)
{
    $result = array(
        'num' => 0,
        'bytes' => 0,
        'lines' => 0,
    );
    $counted = array();
    foreach ($after as $afterInfo) {
        $handle = $afterInfo['handle'];

        $found = false;
        foreach ($before as $beforeInfo) {
            if ($beforeInfo['handle'] == $handle) {
                $found = true;
                break;
            }
        }
        if ($found) {
            continue;
        }

        if (!isset($counted[$handle])) { // Count only once, when same handle is split into own and overridden
            $result['num']++;
            $counted[$handle] = true;
        }
        $result['bytes'] += $afterInfo['bytes'];
        $result['lines'] += $afterInfo['lines'];
    }
    return $result;
}

function get_stats_moved_to_own_handles($before, $after)
{
    $result = array(
        'num' => 0,
        'bytes_before' => 0,
        'bytes_after' => 0,
        'lines_before' => 0,
        'lines_after' => 0,
    );

    foreach ($before as $beforeInfo) {
        if ($beforeInfo['is_own']) {
            continue;
        }
        $handle = $beforeInfo['handle'];

        // Find whether own handle existed before
        $foundBeforeOwn = null;
        foreach ($before as $info) {
            if (($info['handle'] == $handle) && $info['is_own']) {
                $foundBeforeOwn = $info;
            }
        }

        // Find what happened after
        $foundAfterOwn = null;
        $foundAfterOverridden = null;
        foreach ($after as $afterInfo) {
            if ($afterInfo['handle'] != $handle) {
                continue;
            }
            if ($afterInfo['is_own']) {
                $foundAfterOwn = $afterInfo;
            } else {
                $foundAfterOverridden = $afterInfo;
            }
        }
        if ($foundAfterOverridden || !$foundAfterOwn) {
            continue;
        }

        // Caclulate
        $result['num']++;
        $result['bytes_before'] += $beforeInfo['bytes'];
        $result['lines_before'] += $beforeInfo['lines'];
        if ($foundBeforeOwn) {
            $result['bytes_before'] += $foundBeforeOwn['bytes'];
            $result['lines_before'] += $foundBeforeOwn['lines'];
        }
        $result['bytes_after'] += $foundAfterOwn['bytes'];
        $result['lines_after'] += $foundAfterOwn['lines'];
    }

    return $result;
}
