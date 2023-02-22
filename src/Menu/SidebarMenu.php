<?php
declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SidebarMenu
{
    public function __construct(
        private readonly FactoryInterface              $factory,
        private readonly AuthorizationCheckerInterface $auth)
    {
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes(['class' => 'sidebar-nav']);

        $menu->addChild('Home', ['route' => 'home'])
            ->setExtra('icon', 'nav-icon cil-home')
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link');

        $menu->addChild('Work')->setAttribute('class', 'nav-title');

//        $menu->addChild('Projects', ['route' => 'work.projects'])
//            ->setExtra('routes', [
//                ['route' => 'work.projects'],
//                ['pattern' => '/^work\.projects\..+/']
//            ])
//            ->setExtra('icon', 'nav-icon icon-briefcase')
//            ->setAttribute('class', 'nav-item')
//            ->setLinkAttribute('class', 'nav-link');

        if ($this->auth->isGranted('ROLE_WORK_MANAGE_MEMBERS')) {
            $menu->addChild('Members', ['route' => 'work.members'])
                ->setExtra('routes', [
                    ['route' => 'work.members'],
                    ['pattern' => '/^work\.members\..+/']
                ])
                ->setExtra('icon', 'nav-icon cil-people')
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
        }

        $menu->addChild('Control')->setAttribute('class', 'nav-title');

        if ($this->auth->isGranted('ROLE_MANAGE_USERS')) {
            $menu->addChild('Users', ['route' => 'users'])
                ->setExtra('icon', 'nav-icon cib-myspace')
                ->setExtra('routes', [
                    ['route' => 'users'],
                    ['pattern' => '/^users\..+/']
                ])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
        }

        $menu->addChild('Profile', ['route' => 'profile'])
            ->setExtra('icon', 'nav-icon cib-opsgenie')
            ->setExtra('routes', [
                ['route' => 'profile'],
                ['pattern' => '/^profile\..+/']
            ])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link');

        return $menu;
    }
}