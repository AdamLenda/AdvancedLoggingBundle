<?php
namespace AdvancedLoggingBundle\Writer;

interface ILogWriter
{
    public function flush();
    public function write($logUUID, $logLevel, $header, $detail, $indexes, $callSource);
}