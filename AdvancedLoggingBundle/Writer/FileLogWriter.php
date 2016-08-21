<?php
namespace AdvancedLoggingBundle\Writer;

use Symfony\Component\Filesystem\Filesystem;

class FileLogWriter implements ILogWriter
{
    /**
     * @var string
     */
    private $logFilePath;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $logFileDirectory;

    /**
     * FileLogWriter constructor.
     * @param Filesystem $filesystem
     * @param $logFilePath
     */
    public function __construct(Filesystem $filesystem, $logFilePath)
    {
        $this->logFilePath = $logFilePath;
        $this->logFileDirectory = pathinfo($logFilePath, PATHINFO_DIRNAME);
        $this->filesystem = $filesystem;

        if (!$this->filesystem->exists($this->logFileDirectory)) {
            $this->filesystem->mkdir($this->logFileDirectory, 0770);
        }

        $this->filesystem->touch($this->logFilePath);
    }

    public function write($logUUID, $logLevel, $header, $detail, $logIndex, $logSource)
    {
        $logLevelName = LogLevel::getTypeName($logLevel);
        $string = implode("\n".$logUUID.":$logLevelName\t", explode("\n", $header));

        $string .= implode("\n".$logUUID.":$logLevelName\t", explode("\n", $detail));

        file_put_contents($this->logFilePath, $string, FILE_APPEND);
    }

    public function flush()
    {
        // TODO: Implement flush() method.
    }
}