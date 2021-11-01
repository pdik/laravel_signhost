<?php

namespace pdik\signhost\Models;

class ScribbleVerification
{

    /** @var bool */
    public $RequireHandsignature;

    /** @var bool */
    public $ScribbleNameFixed;

    /** @var string */
    public $ScribbleName;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->Type;
    }

    /**
     * @param  mixed  $Type
     * @return ScribbleVerification
     */
    public function setType($Type)
    {
        $this->Type = $Type;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRequireHandsignature()
    {
        return $this->RequireHandsignature;
    }

    /**
     * @param  mixed  $Handsignature
     * @return ScribbleVerification
     */
    public function setRequireHandsignature(bool $Handsignature = true)
    {
        $this->RequireHandsignature = $Handsignature;
        return $this;
    }

    /**
     * @return bool
     */
    public function getScribbleNameFixed()
    {
        return $this->ScribbleNameFixed;
    }

    /**
     * @param  bool  $fixed
     * @return $this
     */
    public function setScribbleNameixed(bool $fixed = false)
    {
        $this->ScribbleNameFixed = $fixed;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setScribbleName($name)
    {
        $this->ScribbleName = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getScribbleName()
    {
        return $this->ScribbleName;
    }


    /**
     * @param  bool  $requireHandsignature
     * @param  bool  $scribbleNameFixed
     * @param  string|null  $scribbleName
     */
    function __construct(
        bool $requireHandsignature,
        bool $scribbleNameFixed,
        string $scribbleName = null
    ) {
        $this->RequireHandsignature = $requireHandsignature;
        $this->ScribbleNameFixed = $scribbleNameFixed;
        $this->ScribbleName = $scribbleName;
    }

    function jsonSerialize()
    {
        return array_filter(array(
            "Type" => $this->Type,
            "RequireHandsignature" => $this->RequireHandsignature,
            "ScribbleNameFixed" => $this->ScribbleNameFixed,
            "ScribbleName" => $this->ScribbleName,
        ));
    }

}