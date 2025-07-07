<?php

namespace Tualo\Office\Security;

use Garden\Cli\Cli;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\Path;
use GuzzleHttp\Client;

class BaseSecurityCommand
{

    public static function checkURIAccess($file): bool
    {
        if (App::configuration('security', 'base_url', false) === false) {
            PostCheck::formatPrintLn(['red'], "base_url is not set configuration");
        } else {
            $url =  App::configuration('security', 'base_url');
            $backendPath =  App::configuration('security', 'backend_path');
            if ($backendPath == false) {
                PostCheck::formatPrintLn(['red'], "backend_path is not set configuration");
            } else {
                try {
                    $client = new Client(
                        [
                            'base_uri' =>  $url,
                            'timeout'  => 1.0,
                        ]
                    );
                    $response = $client->get($backendPath . '/composer.json', []);
                    $code = $response->getStatusCode(); // 200

                    if ($code == 200) {
                        return true;
                    } else {
                        return false;
                    }
                } catch (\Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }

    public static function perform(string $cmdString, string $clientName)
    {
        $cmd = explode(' ', $cmdString);
        if ($clientName != '') $cmd[] = '--client=' . $clientName;
        $classes = get_declared_classes();
        foreach ($classes as $cls) {
            $class = new \ReflectionClass($cls);
            if ($class->implementsInterface('Tualo\Office\Basic\ICommandline')) {
                if ($cmd[0] == $cls::getCommandName()) {
                    $cli = new Cli();
                    $cls::setup($cli);
                    $args = $cli->parse(['./tm', ...$cmd], true);
                    $cls::run($args);
                }
            }
        }
    }
}
