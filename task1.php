<?php

$dir = "D:/xampp/htdocs/nomediatest";
$files = scandir($dir);

sort($files);

foreach($files as $file){
    if(is_file($dir . '/' .$file) && preg_match('/^[a-zA-Z0-9]+\.(ixt)$/',$file)){
        echo $file . "</br>";
    }
}

?>
