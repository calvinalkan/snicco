<?php

declare(strict_types=1);

use Snicco\Bundle\BetterWPDB\BetterWPDBBundle;
use Snicco\Component\Kernel\ValueObject\Environment;

return [
    Environment::ALL => [BetterWPDBBundle::class],
];