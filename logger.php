<?php

class Logger
{
    const LOG_DIR_DEFAULT =  __DIR__ . "/log";
    const LOG_FILE_DEFAULT = "main.log";

    /**
     *  Log message to file
     *
     * @param string $message
     * @param string $level Default: "DEBUG"
     * @param string $file Default: "main.log"
     * @param string $dir Default: "log"
     */
    public function debugLog($message, $level = "DEBUG", $file = self::LOG_FILE_DEFAULT, $dir = self::LOG_DIR_DEFAULT)
    {
        error_log(
            $this->makeLogMessage($message, $level),
            3,
            $dir . "/" . $file
        );
    }

    /**
     *  Generate log message
     *
     * @param string $message
     * @param string $level Default: "DEBUG"
     * @return String Date Level Message File Line,
     *
     */
    public function makeLogMessage($message = "", $level = "DEBUG")
    {
        $trace = debug_backtrace();

        return date('c') .
            "\t" .
            $level .
            "\t" .
            $message .
            "\t".
            "in " . $trace[1]['file'] .
            " line " . $trace[1]['line'] .
            "\n";
    }
}