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
namespace Lujihong\Cache\Aspect;

use Hyperf\Di\Aop\ProceedingJoinPoint;

class CachePutAspect extends \Hyperf\Cache\Aspect\CachePutAspect
{
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $className = $proceedingJoinPoint->className;
        $method = $proceedingJoinPoint->methodName;
        $arguments = $proceedingJoinPoint->arguments['keys'];
        if ($instance = $proceedingJoinPoint->getInstance()) {
            $arguments['this'] = $instance;
        }

        [$key, $ttl, $group] = $this->annotationManager->getCachePutValue($className, $method, $arguments);

        $driver = $this->manager->getDriver($group);

        $result = $proceedingJoinPoint->process();

        $driver->set($key, $result, $ttl);

        return $result;
    }
}
