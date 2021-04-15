<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Lujihong\Cache;

use Hyperf\Cache\Helper\StringHelper;
use Hyperf\Utils\Str;

class AnnotationManager extends \Hyperf\Cache\AnnotationManager
{
    protected function getFormatedKey(string $prefix, array $arguments, ?string $value = null): string
    {
        if ($value !== null) {
            if ($matches = StringHelper::parse($value)) {
                foreach ($matches as $search) {
                    $k = str_replace(['#{', '}'], '', $search);

                    $value = Str::replaceFirst($search, (string) data_get($arguments, $k), $value);
                }
            }
        } else {
            $value = implode(':', $arguments);
        }
        return $prefix . ':' . md5($value);
    }
}
