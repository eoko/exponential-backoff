<?php
namespace Eoko\ExponentialBackoff;

class Module
{
    public function getConfig()
    {
        return [
            'controllers' => [
                'invokables' => [
                    'Eoko\ExponentialBackoff' =>
                        'Eoko\ExponentialBackoff\Utils\ExponentialBackoff',
                ],
            ],
        ];
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }
}
