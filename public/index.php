<?php
declare( strict_types=1 );

use Dotenv\Dotenv;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

try 
{
    $rootPath = realpath( '..' );
    require_once $rootPath . '/vendor/autoload.php';

    Dotenv::createImmutable( $rootPath )->load();

    $di = new FactoryDefault();
    $di->offsetSet( 'rootPath', function () use ( $rootPath ) {
        return $rootPath;
    } );

    $providers = $rootPath . '/config/providers.php';
    if ( !file_exists( $providers ) || !is_readable( $providers ) ) {
        throw new Exception( 'File providers.php does not exist or is not readable.' );
    }

    $providers = include_once $providers;
    foreach ( $providers as $provider ) {
        $di->register( new $provider() );
    }

    ( new Application( $di ) )
        ->handle( $_SERVER['REQUEST_URI'] )
        ->send();

} catch ( Exception $e ) 
{
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString(  ) . '</pre>';
}
