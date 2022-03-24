<?php

namespace Http\Discovery\Strategy;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CommonPsr17ClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [
        RequestFactoryInterface::class => [
            'GuzzleHttp\Psr7\HttpFactory',
        ],
        ResponseFactoryInterface::class => [
            'GuzzleHttp\Psr7\HttpFactory',
        ],
        ServerRequestFactoryInterface::class => [
            'GuzzleHttp\Psr7\HttpFactory',
        ],
        StreamFactoryInterface::class => [
            'GuzzleHttp\Psr7\HttpFactory',
        ],
        UploadedFileFactoryInterface::class => [
            'GuzzleHttp\Psr7\HttpFactory',
        ],
        UriFactoryInterface::class => [
            'GuzzleHttp\Psr7\HttpFactory',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }

        return $candidates;
    }
}
