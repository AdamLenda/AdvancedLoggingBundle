<?php
namespace AdvancedLoggingBundle\Service;

use AdvancedLoggingBundle\LogLevel;
use AdvancedLoggingBundle\Writer\ILogWriter;
use UtilitiesBundle\DebugTools;
use UtilitiesBundle\DTT;
use UtilitiesBundle\StringTools;
use UtilitiesBundle\UUID;

class LogService implements ILogService
{
    /**
     * @var array
     */
    private $logWriters;
    /**
     * @var array
     */
    private $logBuffer;
    /**
     * @var int LogLevel
     */
    private $maxLogLevel = LogLevel::DEBUG;
    /**
     * @var string unique log group identifier
     */
    private $logBatchUUID;
    /**
     * @var array
     */
    private $logIndexBuffer;
    /**
     * @var \DateTime
     */
    private $logBatchCreated;

    public function __construct()
    {
        $this->logBatchUUID = UUID::generate();
        $this->logBatchCreated = DTT::getCurrentTimeWithServerTimeZone();
    }
    
    public function __destruct()
    {
        $this->flush();
    }

    public function flush()
    {
        if (empty($this->logWriters) || empty($this->logBuffer)) {
            return;
        }

        $logText =  array_reduce(
            $this->logBuffer,
            function ($combinedLog, BufferedLog $log) {
                $combinedLog .= sprintf(
                    "%s: %s\nHeader:\n\t%s\n",
                    LogLevel::getTypeName($log->getLogLevel()),
                    $log->getCallSource(),
                    StringTools::tabPadNewLines($log->getHeader(), 1)
                );

                if (!empty($log->getDetail())) {
                    $combinedLog .= sprintf("Detail:\n\t%s\n", StringTools::tabPadNewLines($log->getDetail(), 1));
                }

                return $combinedLog;
            },
            ''
        );

        $logHeader = sprintf(
            "Log: %s\nInitiated:%s\n",
            $this->logBatchUUID,
            $this->logBatchCreated->format('Y-m-d H:i:s')
        );

        if (!empty($this->logWriters)) {
            foreach ($this->logWriters as $logger) {
                $logger->write(
                    $this->logBatchUUID,
                    $this->maxLogLevel,
                    $logHeader,
                    $logText,
                    $this->logIndexBuffer,
                    $this->logBuffer[0]->getCallSource()
                );
            }
        }
    }

    public function registerLogger(ILogWriter $logger)
    {
        $this->logWriters[] = $logger;
    }

    public function debug($header, $detail = null, array $indexes = null, $buffer = true)
    {
        $this->logReceived(
            LogLevel::DEBUG,
            $header,
            $detail,
            $indexes,
            $buffer
        );
    }

    public function info($header, $detail = null, array $indexes = null, $buffer = true)
    {
        $this->logReceived(
            LogLevel::INFO,
            $header,
            $detail,
            $indexes,
            $buffer
        );
    }

    public function warn($header, $detail = null, array $indexes = null, $buffer = true)
    {
        $this->logReceived(
            LogLevel::WARN,
            $header,
            $detail,
            $indexes,
            $buffer
        );
    }

    public function error($header, $detail = null, array $indexes = null, $buffer = true)
    {
        $this->logReceived(
            LogLevel::ERROR,
            $header,
            $detail,
            $indexes,
            $buffer
        );
    }

    public function alert($header, $detail = null, array $indexes = null, $buffer = true)
    {
        $this->logReceived(
            LogLevel::ALERT,
            $header,
            $detail,
            $indexes,
            $buffer
        );
    }

    private function logReceived($logLevel, $header, $detail, $indexes, $buffer)
    {
        if ($buffer) {
            $this->logBuffer[] = new BufferedLog($logLevel, $header, $detail, DebugTools::getCallStack(-3));

            if (!empty($indexes)) {
                $this->logIndexBuffer = array_replace_recursive($this->logIndexBuffer, $indexes);
            }
            if ($this->maxLogLevel < $logLevel) {
                $this->maxLogLevel = $logLevel;
            }
        } elseif (!empty($this->logWriters)) {
            foreach ($this->logWriters as $logger) {
                $logger->write(
                    $this->logBatchUUID,
                    $logLevel,
                    $header,
                    $detail,
                    $indexes,
                    DebugTools::getCallStack(-3)
                );
            }
        }
    }
}