<?php declare(strict_types = 1);

namespace AppBundle\Service;

/**
 * Class FileHelper
 * @package AppBundle\Service
 */
class FileHelper
{
    /** @var resource */
    private $file;

    /** @var string */
    private $filename;

    /**
     * @param string $filename
     * @param string $path
     */
    public function __construct(string $filename = null, string $path = "./example_data/")
    {
        if (null !== $filename) {
            $this->createFile($filename, $path);
        }
    }

    /**
     * @param string $filename
     * @param string $path
     */
    public function createFile(string $filename, string $path = "./example_data/")
    {
        if (null !== $filename) {
            $this->filename = $path . $filename;
            if (true === file_exists($path . $filename)) {
                $this->file = fopen($this->filename, 'r');
            } else {
                $this->file = fopen($this->filename, 'w');
            }
        }
    }

    /**
     * @param string|array $content
     */
    public function writeToFile($content) {
        if ($this->file) {
            file_put_contents($this->filename, $content, FILE_APPEND | LOCK_EX);
        } else {
            throw new \LogicException('At first create the file!');
        }
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * @return resource
     */
    public function getFileResource()
    {
        return $this->file;
    }

    /**
     * @param resource $file
     */
    public function setFileResource($file)
    {
         $this->file = $file;
    }
}