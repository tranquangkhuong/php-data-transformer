<?php

require 'vendor/autoload.php';

use Tranquangkhuong\DataTransformer\DataTransformer;

$config = file_get_contents(__DIR__ . '/config_lltp.json');

$dataApp1 = file_get_contents(__DIR__ . '/data.json');
$dataApp1 = json_decode($dataApp1, true);

try {
    $transformer = new DataTransformer();
    $transformer->config($config)->data($dataApp1)->case('send');
    $result = $transformer->transform(from: 'vneid', to: 'qllltp');
    file_put_contents(__DIR__ . '/result.json', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    dd($result);
} catch (Exception $e) {
    dd('Exception', $e->getMessage(), $e->getFile(), $e->getLine());
}
