<?php
$fetch = file_get_contents('https://127.0.0.1:8000/public/test.txt');
$decode = json_decode($fetch,true);

?>


<!DOCTYPE html>
<html lang="en">
<head></head>
<body></body>
</html>