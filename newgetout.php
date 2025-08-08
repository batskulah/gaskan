<?php
/* Author: Sohay | Modified by ChatGPT | Version: 1.4 */

echo '<style>
body { background-image: url(https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhrvxStLhBPGyJw3BGcAvO-tYwdJ2mxwpYnBQWbwceJBhmJwNdwCNcNZHFTEqFFw_av0b-sVtEzgWqNr2yWx1eY0GpLQQxIirppzSz_QRz6whI-rogxtPNJGxnGsgGjCoW3QtAjgyNHXggsxmn2IVnqOCryK3FCmCvrKAj-uBT08kzefu9xQ45JDLEyY80/s1800/rakunnew.png);
     font-family:Courier New, monospace;}
input, textarea { background-color:#000; color:#0f0; border:1px solid #0f0; font-family:monospace; }
a { color:#0f0; text-decoration:none; }
a:hover { background:#f00; }
#result-container {
    width: 50%;
    background-color: #000;
    color: #0f0;
    padding: 10px;
    border: 1px solid #0f0;
    margin-top: 15px;
}
textarea { width: 100%; height: 200px; margin-top:10px; }
</style>';

set_time_limit(0);
error_reporting(0);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);

$path = getcwd();
$suspect_exts = ['php', 'phtml', 'shtml', 'inc','pgif'];

if (isset($_GET['dir'])) {
    $path = $_GET['dir'];
}

if (isset($_GET['kill'])) {
    unlink(__FILE__);
    die("Script deleted.");
}

echo "<a href='?kill'><font color='yellow'>HAPUS SCANNERNYA</font></a><br>";
echo '<form action="" method="get">
<input type="text" name="dir" value="' . htmlspecialchars($path) . '" style="width:500px;">
<input type="submit" value="Scan">
</form><br>';
echo "CURRENT DIR: <font color='yellow'>" . htmlspecialchars($path) . "</font><br><br>";

if (isset($_GET['delete'])) {
    $target = $_GET['delete'];
    if (file_exists($target)) {
        unlink($target);
        echo "File deleted: <font color='yellow'>" . htmlspecialchars($target) . "</font><br>";
    } else {
        echo "File not found or already deleted.<br>";
    }
    exit;
}

if (isset($_GET['delete_same_content'])) {
    $target = $_GET['delete_same_content'];
    if (!file_exists($target)) {
        echo "Target file tidak ditemukan.";
        exit;
    }

    $targetContent = file_get_contents($target);
    echo "<div id='result-container'>";
    echo "Scanning to delete all files with same content...<br><br>";
    deleteFilesWithSameContent($path, $targetContent, $target);
    echo "</div>";
    exit;
}

if (isset($_GET['delete_all'])) {
    if (file_exists("hasil-scan.txt")) {
        $files = file("hasil-scan.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
                echo "Deleted: <font color='red'>" . htmlspecialchars($file) . "</font><br>";
            }
        }
        unlink("hasil-scan.txt");
        echo "<br><b><font color='yellow'>All suspect files deleted.</font></b><br>";
    } else {
        echo "No hasil-scan.txt found.<br>";
    }
    exit;
}

scanBackdoor($path);

if (file_exists("hasil-scan.txt")) {
    echo "<br><a href='?delete_all=1&dir=" . urlencode($path) . "'><font color='red'>[Delete All Found Files]</font></a><br>";
}

// ========== FUNCTIONS ==========

function save($fname, $value) {
    $file = fopen($fname, "a");
    fwrite($file, $value);
    fclose($file);
}

function checkBackdoor($file_location) {
    global $path;
    $pattern = "#(eval|exec|system|passthru|shell_exec|base64_decode|gzinflate|assert|file_put_contents|file_get_contents|move_uploaded_file|create_function|preg_replace\s*\(\s*['\"]\s*/e)#i";
    $contents = @file_get_contents($file_location);

    if ($contents !== false && preg_match($pattern, $contents)) {
        echo "<div id='result-container'>";
        echo "[+] Suspect file → 
        <a href='?delete=" . urlencode($file_location) . "&dir=" . urlencode($path) . "'><font color='red'>| HAPUS FILE INI |</font></a> 
        <a href='?delete_same_content=" . urlencode($file_location) . "&dir=" . urlencode($path) . "'><font color='yellow'>HAPUS SEMUA SCRIPT YANG SAMA DENGAN FILE INI(Cuma sisa File Ini Saja)</font></a><br>";
        echo "<font color='red'>" . htmlspecialchars($file_location) . "</font><br>";
        echo '<textarea readonly>' . htmlspecialchars(substr($contents, 0, 2000)) . '</textarea><br>';
        echo "</div>";
        save("hasil-scan.txt", "$file_location\n");
    }
}

function scanBackdoor($dir) {
    global $suspect_exts;
    if (!is_readable($dir)) return;

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $file_path = $dir . '/' . $item;
        $basename = basename($file_path);

        foreach ($suspect_exts as $ext) {
            if (preg_match("/\." . preg_quote($ext, '/') . "$/i", $basename) || $basename[0] === '.') {
                if (is_file($file_path)) {
                    checkBackdoor($file_path);
                }
                break;
            }
        }

        if (is_dir($file_path)) {
            scanBackdoor($file_path);
        }
    }
}

function deleteFilesWithSameContent($dir, $targetContent, $originFile) {
    global $suspect_exts;
    if (!is_readable($dir)) return;

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $file_path = $dir . '/' . $item;
        $basename = basename($file_path);

        foreach ($suspect_exts as $ext) {
            if (preg_match("/\." . preg_quote($ext, '/') . "$/i", $basename) || $basename[0] === '.') {
                if (is_file($file_path) && is_readable($file_path)) {
                    if (realpath($file_path) !== realpath($originFile)) {
                        $content = file_get_contents($file_path);
                        if ($content === $targetContent) {
                            unlink($file_path);
                            echo "Deleted matching content file: <font color='red'>" . htmlspecialchars($file_path) . "</font><br>";
                        }
                    }
                }
                break;
            }
        }

        if (is_dir($file_path)) {
            deleteFilesWithSameContent($file_path, $targetContent, $originFile);
        }
    }

    echo "<br><b><font color='yellow'>Selesai hapus semua file dengan isi yang sama.</font></b><br>";
}
?>
