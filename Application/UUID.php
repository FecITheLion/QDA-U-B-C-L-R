<?php

class UUID implements \JsonSerializable
{
    public readonly int $at;
    public readonly string $id;
    public readonly mixed $oem;
    public Label $label;

    public function __construct(mixed &$oem = null, ?int $at = null)
    {
        $this->at = $at;
        $this->id = uniqid ($oem->label, true);
        $this->oem = $oem;
    }

    public function __toString() : string
    {
        $A = json_encode ($this->jsonSerialize ());
        if ($A)
        {
            return $A;
        }
        return $this->id;
    }

    public function jsonSerialize() : mixed
    {
        return [
            "at" => $this->at,
            "id" => $this->id,
            "oem" => $this->oem,
            "label" => $this->label,
        ];
    }
}