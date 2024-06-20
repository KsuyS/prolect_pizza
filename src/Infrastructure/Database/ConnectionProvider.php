<?php
declare(strict_types=1);
namespace App\Infrastructure\Database;

use App\Config;



class ConnectionProvider
{
    public static function connectDatabase(): \PDO
    {
        return new \PDO(Config::getDatabaseDsn(), Config::getDatabaseUser(), Config::getDatabasePassword());
    }
}