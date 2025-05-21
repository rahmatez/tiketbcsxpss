<?php

return [    'pdf' => [
        'enabled' => true,
        'binary'  => '"' . env('WKHTMLTOPDF_PATH', 'C:/Program Files/wkhtmltopdf/bin/wkhtmltopdf.exe') . '"',
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],    'image' => [
        'enabled' => true,
        'binary'  => '"' . env('WKHTMLTOIMAGE_PATH', 'C:/Program Files/wkhtmltopdf/bin/wkhtmltoimage.exe') . '"',
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],
];
