<?php
namespace Library;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\CouchDB\HTTP\SocketClient;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\ODM\CouchDB\Configuration;
use Doctrine\ODM\CouchDB\DocumentManager;

class DoctrineCouchDBConnector
{
    public static function create($container)
    {
        // we may use config later using data from $container
        $annotationNamespace = 'Doctrine\ODM\CouchDB\Mapping\Annotations';
        $annotationPath = BASEPATH . 'vendor/doctrine/couchdb-odm/lib';

        AnnotationRegistry::registerAutoloadNamespace($annotationNamespace, $annotationPath);

        $databaseName = "doctrine";
        $documentPaths = array("Entity");
        $httpClient = new SocketClient('couchdb', '5984');
        $dbClient = new CouchDBClient($httpClient, $databaseName);

        $config = new Configuration();
        $metadataDriver = $config->newDefaultAnnotationDriver($documentPaths);

        $config->setProxyDir(BASEPATH . "proxies");
        $config->setMetadataDriverImpl($metadataDriver);

        $documentManager = new DocumentManager($dbClient, $config);

        return $documentManager;
    }
}
