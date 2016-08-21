<?php
namespace AdvancedLoggingBundle\Service;

class BufferedLog
{
    /**
     * BufferedLog constructor.
     * @param $logLevel
     * @param $header
     * @param $detail
     * @param $callSource
     */
    public function __construct($logLevel, $header, $detail, $callSource)
    {
        $this->callSource = $callSource;
        $this->detail = $detail;
        $this->header = $header;
        $this->logLevel = $logLevel;
    }

    /**
     * @var string
     */
    protected $callSource;

    /**
     * @param string
     * @return $this
     */
    public function setCallSource($callSource)
    {
        $this->callSource = $callSource;
        return $this;
    }

    /**
     * @return string
     */
    public function getCallSource()
    {
        return $this->callSource;
    }

    /**
     * @var string
     */
    protected $header;

    /**
     * @param string
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @var string
     */
    protected $detail;

    /**
     * @param string
     * @return $this
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
        return $this;
    }

    /**
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @var int
     */
    protected $logLevel;

    /**
     * @param int
     * @return $this
     */
    public function setLogLevel($logLevel)
    {
        $this->logLevel = $logLevel;
        return $this;
    }

    /**
     * @return int
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }
}