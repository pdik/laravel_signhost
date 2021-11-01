<?php
namespace pdik\signhost\Exception;

/**
 * Class SignhostException
 *
 * @package   pdik\signhost
 * @author    Pepijn dik <pepijn@pdik.nl>
 */
class SignhostException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}