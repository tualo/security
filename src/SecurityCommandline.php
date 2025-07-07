<?php

namespace Tualo\Office\Security;

use Garden\Cli\Cli;
use Garden\Cli\Args;
use Tualo\Office\Basic\ICommandline;
use Tualo\Office\Security\ISecurityCommandline;

class SecurityCommandline  implements ICommandline
{


    public static function getCommandName(): string
    {
        return 'security';
    }

    public static function getCommandDescription(): string
    {
        return '**';
    }
    public static function setup(Cli $cli)
    {
        $cli->command(self::getCommandName())
            ->description(self::getCommandDescription())
            ->opt('client', 'only use this client', false, 'string');
    }

    public static function security(Cli $cli): void
    {

        $classes = get_declared_classes();
        foreach ($classes as $cls) {
            $class = new \ReflectionClass($cls);
            if ($class->implementsInterface('Tualo\Office\Security\ISecurityCommandline')) {
                $description = $cls::getCommandDescription();
                $cli->command(self::getCommandName() . ' ' . $cls::getCommandName())
                    ->description($description)
                    ->opt('client', 'only use this client', false, 'string');
            }
        }

        $cli->command(self::getCommandName())
            ->description('show all security commands');
    }

    public static function run(Args $args)
    {
        if (count($args->getArgs()) == 0) {
            $classes = get_declared_classes();
            foreach ($classes as $cls) {
                $class = new \ReflectionClass($cls);
                if ($class->implementsInterface('Tualo\Office\Security\ISecurityCommandline')) {
                    echo "./tm " . self::getCommandName() . ' ' . $cls::getCommandName() . PHP_EOL;
                }
            }
        } else {

            $classes = get_declared_classes();
            $argv = $GLOBALS["argv"];
            array_shift($argv);
            $argv[0] = $GLOBALS["argv"][0];

            foreach ($classes as $cls) {
                $class = new \ReflectionClass($cls);
                if ($class->implementsInterface('Tualo\Office\Security\ISecurityCommandline')) {
                    if ($cls::getCommandName() == $argv[1]) {
                        $cli = new Cli();
                        $cls::security($cli);
                        $args = $cli->parse($argv, true);
                        $cls::run($args);
                    }
                }
            }
        }
    }
}
