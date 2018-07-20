<?php

namespace ShoppingCartBundle\DataFixtures\ORM;

use Nelmio\Alice\Loader\NativeLoader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use ShoppingCartBundle\Entity\Category;


class ShoppingCartDataFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {


        $loader = new  NativeLoader();
        $objectSet = $loader->loadFile(__DIR__ . '/characters.yml')->getObjects();

        foreach($objectSet as $DataItem) {
            $manager->persist($DataItem);
        }
        $manager->flush();
    }

}
