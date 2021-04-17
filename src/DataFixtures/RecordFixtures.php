<?php

namespace App\DataFixtures;

use App\Entity\Record;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $this->fileUploader = new FileUploader($this->params->get('app.records_path'), $this->slugger);
    }

    public function load(ObjectManager $manager)
    {
        $this->cleanRecordsFolder();
        for ($i = 0; $i < 30; $i++) {
            $record = new Record();
            $file = $this->getTestFile();
            $record->setRecordName($file->getFilename());
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
        return new UploadedFile($targetPath, $fileName, "audio/mpeg", null, true);
    }

    private function cleanRecordsFolder()
    {
        $files = glob($this->params->get('app.records_path') . '/**');
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
    }
}
