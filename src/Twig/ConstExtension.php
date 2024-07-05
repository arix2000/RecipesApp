<?php

declare(strict_types=1);

namespace App\Twig;

use App\Constants\RoutesConst;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConstExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_constant', [$this, 'getConstant']),
        ];
    }

    public function getConstant(string $name)
    {
        return constant(RoutesConst::class . '::' . $name);
    }
}
