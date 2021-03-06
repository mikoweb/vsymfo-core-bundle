<?php

/*
 * This file is part of the vSymfo package.
 *
 * website: www.vision-web.pl
 * (c) Rafał Mikołajun <rafal@vision-web.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vSymfo\Bundle\CoreBundle\EventListener;

use Liip\ThemeBundle\ActiveTheme;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Http\AccessMap;

/**
 * Custom exceptions listener.
 *
 * @author Rafał Mikołajun <rafal@vision-web.pl>
 * @package vSymfo Core Bundle
 * @subpackage EventListener
 */
class ExceptionListener implements ContainerAwareInterface
{
    /**
     * @var AccessMap
     */
    private $accessMap;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ActiveTheme
     */
    private $activeTheme;

    /**
     * @var string
     */
    private $backendTheme;

    /**
     * @param AccessMap $accessMap
     * @param ActiveTheme $activeTheme
     * @param string $backendTheme
     */
    public function __construct(AccessMap $accessMap, ActiveTheme $activeTheme, $backendTheme)
    {
        $this->accessMap = $accessMap;
        $this->activeTheme = $activeTheme;
        $this->backendTheme = $backendTheme;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $isPanel = false;
        foreach ($this->accessMap->getPatterns($event->getRequest()) as $pattern) {
            if (is_array($pattern)) {
                foreach ($pattern as $role) {
                    if (is_string($role) && $role === 'ROLE_PANEL_ACCESS') {
                        $isPanel = true;
                        break;
                    }
                }
            }
        }

        if ($isPanel) {
            $this->activeTheme->setName('backend_' . $this->backendTheme);
        }
    }
}
