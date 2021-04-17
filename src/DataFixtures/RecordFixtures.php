<?php

namespace App\DataFixtures;

use App\Entity\Record;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecordFixtures extends Fixture
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * RecordFixtures constructor.
     * @param Filesystem $filesystem
     * @param SluggerInterface $slugger
     */
    public function __construct(Filesystem $filesystem, SluggerInterface $slugger)
    {
        $this->filesystem = $filesystem;
        $this->slugger = $slugger;
        $this->fileUploader = new FileUploader('public/songs/records', $this->slugger);
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $record = new Record();
            $file = $this->getTestFile();
            $filename = $this->fileUploader->upload($file);
            $record->setRecordName($filename);
            $record->setRecordFile($file);
            $manager->persist($record);
        }

        $manager->flush();
    }

    private function getTestFile() : File
    {
        $fileName = 'mp3test.mp3';
        $originalPath = __DIR__ . '/fixtures_files/' . $fileName;
        $targetPath = sys_get_temp_dir() . '/' . $fileName;

        $this->filesystem->copy($originalPath, $targetPath, false);
        $file = new File($targetPath, $fileName, "audio/mpeg", null, true);
        return $file;
    }
}
