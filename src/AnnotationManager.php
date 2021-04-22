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
        //Avoid multiple hyperf projects using one redis on the same server, leading to key conflicts
        $appName = $this->config->get('app_name', 'hyperf');

        if ($value !== null) {
            if ($matches = StringHelper::parse($value)) {
                foreach ($matches as $search) {
                    $k = str_replace(['#{', '}'], '', $search);

                    $value = Str::replaceFirst($search, (string) data_get($arguments, $k), $value);
                }
            }
        } else {
            $val = array_values($arguments);
            $value = implode(':', $val);
        }

        return $prefix . ':' . md5($appName . $value);
    }
}
