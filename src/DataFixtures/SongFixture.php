<?php

namespace App\DataFixtures;

use App\Entity\Song;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class SongFixture extends Fixture
{
    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * SongFixture constructor.
     * @param ContainerBagInterface $params
     */
    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < $this->params->get('fixtures.number_of_songs'); $i ++) {
            $song = new Song();
            $song->setName('Song name ' . $i);
            $manager->persist($song);
            $this->addReference('song_' . $i, $song);
        }

        $manager->flush();
    }
}
