<?php return array(
    'root' => array(
        'name' => 'minichan/minichan',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => NULL,
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'google/protobuf' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '1fb247e72df401c863ed239c1660f981644af5db',
            'type' => 'library',
            'install_path' => __DIR__ . '/../google/protobuf',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
        'minichan/minichan' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => NULL,
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'swoole/ide-helper' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '07692fa8f1bb8eac828410acd613ea5877237b09',
            'type' => 'library',
            'install_path' => __DIR__ . '/../swoole/ide-helper',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
    ),
);
