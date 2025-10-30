<?php

namespace Tualo\Office\Security;

use Garden\Cli\Args;
use Garden\Cli\Cli;

interface ISecurityCommandline
{
    public static function getCommandName(): string;
    public static function getCommandDescription(): string;
    public static function security(Cli $cli): void;
    public static function run(Args $args);
}
