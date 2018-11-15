<?php

/*
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Create a new Silex Application with Twig.  Configure it for debugging.
 * Follows Silex Skeleton pattern.
 */
use Google\Cloud\Samples\Bookshelf\DataModel\Sql;
use Google\Cloud\Samples\Bookshelf\DataModel\Datastore;
use Google\Cloud\Samples\Bookshelf\DataModel\MongoDb;
use Google\Cloud\Samples\Bookshelf\FileSystem\CloudStorage;
use Google\Cloud\Samples\Bookshelf\DataModel\RequestReplyProcessor;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Yaml\Yaml;

$app = new Application();

// parse configuration
$config = getenv('MALP_CONFIG') ?:
    __DIR__ . '/../config/' . 'settings.yml';

$app['config'] = Yaml::parse(file_get_contents($config));

$app['malp.reply'] = new RequestReplyProcessor();

$config = $app['config'];
    $projectId = $config['google_project_id'];
    $bucketName = $projectId . '.appspot.com';
$app['malp.bucketname'] = $bucketName;

// Cloud Storage
$app['malp.storage'] = function ($app) {
    /** @var array $config */
    $config = $app['config'];
    $projectId = $config['google_project_id'];
    $bucketName = $projectId . '.appspot.com';
    return new CloudStorage($projectId, $bucketName);
};

// determine the datamodel backend using the app configuration
$app['malp.model'] = function ($app) {
    /** @var array $config */
    $config = $app['config'];
    if (empty($config['malp_backend'])) {
        throw new \DomainException('"malp_backend" must be set in malp config');
    }

    // Data Model
    switch ($config['malp_backend']) {
        case 'mongodb':
            return new MongoDb(
                $config['mongo_url'],
                $config['mongo_database'],
                $config['mongo_collection']
            );
        case 'datastore':
            return new Datastore(
                $config['google_project_id']
            );
        case 'mysql':
            $mysql_dsn = Sql::getMysqlDsn(
                $config['cloudsql_database_name'],
                $config['cloudsql_port'],
                getenv('GAE_INSTANCE') ? $config['cloudsql_connection_name'] : null
            );
            return new Sql(
                $mysql_dsn,
                $config['cloudsql_user'],
                $config['cloudsql_password']
            );
        case 'postgres':
            $postgres_dsn = Sql::getPostgresDsn(
                $config['cloudsql_database_name'],
                $config['cloudsql_port'],
                getenv('GAE_INSTANCE') ? $config['cloudsql_connection_name'] : null
            );
            return new Sql(
                $postgres_dsn,
                $config['cloudsql_user'],
                $config['cloudsql_password']
            );
        default:
            throw new \DomainException("Invalid \"malp_backend\" given: $config[malp_backend]. "
                . "Possible values are mysql, postgres, mongodb, or datastore.");
    }
};

// Turn on debug locally
if (in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', 'fe80::1', '::1'])
    || php_sapi_name() === 'cli-server'
) {
    $app['debug'] = true;
} else {
    $app['debug'] = filter_var(
        getenv('MALP_DEBUG'),
                               FILTER_VALIDATE_BOOLEAN
    );
}


return $app;
