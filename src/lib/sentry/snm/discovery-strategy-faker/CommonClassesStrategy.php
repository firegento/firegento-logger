<?php

namespace Http\Discovery\Strategy;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\RequestFactory;
use Psr\Http\Message\RequestFactoryInterface as Psr17RequestFactory;
use Http\Message\MessageFactory;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use Http\Message\UriFactory;
use Http\Message\UriFactory\GuzzleUriFactory;
use Psr\Http\Client\ClientInterface as Psr18Client;
use Symfony\Component\HttpClient\HttplugClient as SymfonyHttplug;
use Symfony\Component\HttpClient\Psr18Client as SymfonyPsr18;

/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CommonClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [
        MessageFactory::class => [
            ['class' => GuzzleMessageFactory::class, 'condition' => [GuzzleRequest::class, GuzzleMessageFactory::class]],
        ],
        StreamFactory::class => [
            ['class' => GuzzleStreamFactory::class, 'condition' => [GuzzleRequest::class, GuzzleStreamFactory::class]],
        ],
        UriFactory::class => [
            ['class' => GuzzleUriFactory::class, 'condition' => [GuzzleRequest::class, GuzzleUriFactory::class]],
        ],
        HttpAsyncClient::class => [
            ['class' => SymfonyHttplug::class, 'condition' => [SymfonyHttplug::class, Promise::class, RequestFactory::class, [self::class, 'isPsr17FactoryInstalled']]],
        ],
        HttpClient::class => [
            ['class' => SymfonyHttplug::class, 'condition' => [SymfonyHttplug::class, RequestFactory::class, [self::class, 'isPsr17FactoryInstalled']]],
        ],
        Psr18Client::class => [
            [
                'class' => [self::class, 'symfonyPsr18Instantiate'],
                'condition' => [SymfonyPsr18::class, Psr17RequestFactory::class],
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        if (Psr18Client::class === $type) {
            return self::getPsr18Candidates();
        }

        return self::$classes[$type] ?? [];
    }

    /**
     * @return array The return value is always an array with zero or more elements. Each
     *               element is an array with two keys ['class' => string, 'condition' => mixed].
     */
    private static function getPsr18Candidates()
    {
        $candidates = self::$classes[Psr18Client::class];

        // HTTPlug 2.0 clients implements PSR18Client too.
        foreach (self::$classes[HttpClient::class] as $c) {
            try {
                if (is_subclass_of($c['class'], Psr18Client::class)) {
                    $candidates[] = $c;
                }
            } catch (\Throwable $e) {
                trigger_error(sprintf('Got exception "%s (%s)" while checking if a PSR-18 Client is available', get_class($e), $e->getMessage()), E_USER_WARNING);
            }
        }

        return $candidates;
    }

    public static function symfonyPsr18Instantiate()
    {
        return new SymfonyPsr18(null, Psr17FactoryDiscovery::findResponseFactory(), Psr17FactoryDiscovery::findStreamFactory());
    }

    /**
     * Can be used as a condition.
     *
     * @return bool
     */
    public static function isPsr17FactoryInstalled()
    {
        try {
            Psr17FactoryDiscovery::findResponseFactory();
        } catch (NotFoundException $e) {
            return false;
        } catch (\Throwable $e) {
            trigger_error(sprintf('Got exception "%s (%s)" while checking if a PSR-17 ResponseFactory is available', get_class($e), $e->getMessage()), E_USER_WARNING);

            return false;
        }

        return true;
    }
}
