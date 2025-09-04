<?php
namespace Rivulet\Filesystem\Operations;

class Rename
{
    public static function execute(string $from, string $to): bool
    {
        return Move::execute($from, $to);
    }
}
