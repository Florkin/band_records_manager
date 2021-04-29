<?php

namespace App\DataFixtures;

use App\Entity\Record;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecordFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var SluggerInterface
     */
    private $slugger;
    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * RecordFixtures constructor.
     * @param Filesystem $filesystem
     * @param SluggerInterface $slugger
     * @param ContainerBagInterface $params
     */
    public function __construct(Filesystem $filesystem, SluggerInterface $slugger, ContainerBagInterface $params)
    {
        $this->filesystem = $filesystem;
        $this->slugger = $slugger;
        $this->params = $params;
    }

    public function load(ObjectManager $manager)
    {
        $this->cleanRecordsFolder();
        for ($i = 0; $i < $this->params->get('fixtures.number_of_songs') * 8; $i++) {
            $record = new Record();
            $file = $this->getTestFile();
            $record->setName($file->getFilename());
            $record->setRecordFile($file);
            $record->setSong($this->getReference('song_'.rand(0, $this->params->get('fixtures.number_of_songs') - 1)));
            $record->setRecordedAt((new \DateTime())->modify('-' . $i . ' day'));
            $manager->persist($record);
        }
        $manager->flush();
    }

    private function getTestFile(): File
    {
        $fileName = 'mp3test.mp3';
        $originalPath = __DIR__.'/fixtures_files/'.$fileName;
        $targetPath = sys_get_temp_dir().'/'.$fileName;

        $this->filesystem->copy($originalPath, $targetPath, false);

        return new UploadedFile($targetPath, $fileName, "audio/mpeg", null, true);
    }

    private function cleanRecordsFolder()
    {
        $files = glob($this->params->get('app.absolute_records_path').'/**');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function getDependencies()
    {
        return [
            SongFixture::class,
        ];
    }
}
