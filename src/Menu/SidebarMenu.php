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

        $menu->addChild('Profile', ['route' => 'profile'])
            ->setExtra('icon', 'nav-icon cil-user')
            ->setExtra('routes', [
                ['route' => 'profile'],
                ['pattern' => '/^profile\..+/']
            ])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link');

        return $menu;
    }
}