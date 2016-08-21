<?php
namespace AdvancedLoggingBundle\Service;

interface ILogService
{
    public function debug($header, $detail = null, array $indexes = null, $callSource = null);
    public function info($header, $detail = null, array $indexes = null, $callSource = null);
    public function warn($header, $detail = null, array $indexes = null, $callSource = null);
    public function error($header, $detail = null, array $indexes = null, $callSource = null);
    public function alert($header, $detail = null, array $indexes = null, $callSource = null);
}