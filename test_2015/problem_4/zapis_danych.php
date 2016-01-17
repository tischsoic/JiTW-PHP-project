<?php

function generatePath($fileName) {
    return __DIR__ . '/' . $fileName;
}

$fileName = 'data';
$filePath = generatePath($fileName);
$file = fopen($filePath, 'w') or die('Unable to open file!');
  
$date = date('Y-m-d H:i:s');
$text = $date . "\n";

//  For easy testing I use $_GET instead of $_POST.
if(count($_GET) === 0) {
    $textToSave .= 'Nie przekazano żadnych parametrów!';
} else {
    $postData = '';
    foreach($_GET as $key => $singlePostData) {
        $postData .= 'Parametr ' . $key . ' ma wartość ' . $singlePostData . "\n";
    }
//  Trimming last new line char.
    $textToSave = rtrim($postData);
}

fwrite($file, $textToSave);
fclose($file);