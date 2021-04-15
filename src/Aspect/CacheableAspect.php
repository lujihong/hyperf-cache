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

use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Driver\KeyCollectorInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;

class CacheableAspect extends \Hyperf\Cache\Aspect\CacheableAspect
{
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $className = $proceedingJoinPoint->className;
        $method = $proceedingJoinPoint->methodName;
        $arguments = $proceedingJoinPoint->arguments['keys'];
        if ($instance = $proceedingJoinPoint->getInstance()) {
            $arguments['this'] = $instance;
        }

        [$key, $ttl, $group, $annotation] = $this->annotationManager->getCacheableValue($className, $method, $arguments);

        $driver = $this->manager->getDriver($group);

        [$has, $result] = $driver->fetch($key);
        if ($has) {
            return $result;
        }

        $result = $proceedingJoinPoint->process();

        $driver->set($key, $result, $ttl);
        if ($driver instanceof KeyCollectorInterface && $annotation instanceof Cacheable && $annotation->collect) {
            $driver->addKey($annotation->prefix . 'MEMBERS', $key);
        }

        return $result;
    }
}
