<?php

class Register implements Registry
{
    protected SplObjectStorage $store;
    protected array $posit = [];
    protected int $cardinal = 0;
    public Label $label;

    public function __construct(?string ...$labels)
    {
        $this->label = new Label(...$labels);
        $this->store = new SplObjectStorage();
    }

    public function __sleep() : array
    {
        $variables = [];
        return $variables;
    }

    public function __wakeup()
    {
        
    }

    public function __toString() : string
    {
        $A = json_encode ($this->jsonSerialize ());
        if ($A)
        {
            return $A;
        }
        return $this->label;
    }

    public function jsonSerialize() {
        return [
            "strore" => $this->store,
            "posit" => $this->posit,
            "cardinal" => $this->cardinal,
            "label" => $this->label
        ];
    }

    public function offsetSet(mixed $offset, mixed $info): void
    {
        $type = gettype ($offset);
        switch ($type)
        {
            case "callable":
                $subset = $this->sub ($offset);
                $count = count($subset);
                if ($count)
                {
                    if ($count === 1)
                    {
                        $set = $subset[0];
                        $object = $set["object"];
                        $this->store[$object] = $info;
                        break;
                    }
                    foreach($subset as $set)
                    {
                        $object = $set["object"];
                        $this->store[$object] = $info;
                    }
                }
                break;
            case "string":
            case "integer":
                $isset = isset ($this->posit[$offset]);
                if ($isset)
                {
                    $object = $this->posit[$offset];
                    $isnull = is_null ($object);
                    if (!$isnull)
                    {
                        $isset = isset($this->store[$object]);
                        if ($isset)
                        {
                            $this->store[$object] = $info;
                        }
                    }
                }
                break;
            case "object":
                $isset = isset ($this->store[$offset]);
                if ($isset)
                {
                    $this->store[$offset] = $info;
                }
                break;
            
                $isset = isset ($this->posit[$offset]);
                if ($isset)
                {
                    $object = $this->posit[$offset];
                    $isnull = is_null ($object);
                    if (!$isnull)
                    {
                        $isset = isset($this->store[$object]);
                        if ($isset)
                        {
                            $this->store[$object] = $info;
                        }
                    }
                }
                break;
        }
    }

    public function offsetExists (mixed $offset) : bool
    {
        $exists = false;
        $type = gettype ($offset);
        switch ($type)
        {
            case "callable":
                $subset = $this->sub($offset);
                $count = count($subset);
                if ($count)
                {
                    $exists = true;
                }
                break;
            case "string":
            case "integer":
                $isset = isset($this->posit[$offset]);
                if ($isset)
                {
                    $isnull = is_null($this->posit[$offset]);
                    if (!$isnull)
                    {
                        $isset = isset($this->store[$offset]);
                        if ($isset)
                        {
                            $exists = true;
                        }
                    }
                }
                break;
            case "object":
                $isset = isset($this->store[$offset]);
                if ($isset)
                {
                    $exists = true;
                }
                break;
        }
        return $exists;
    }

    public function offsetGet (mixed $offset) : mixed
    {
        $type = gettype ($offset);
        switch ($type)
        {
            case "callable":
                $subset = $this->sub($offset);
                $count = count($subset);
                if ($count)
                {
                    if ($count === 1)
                    {
                        $set = $subset[0];
                        return $set["info"];
                    }
                    return $subset;
                }
                break;
            case "string":
            case "integer":
                $isset = isset($this->posit[$offset]);
                if ($isset)
                {
                    $object = $this->posit[$offset];
                    $isnull = is_null($object);
                    if (!$isnull)
                    {
                        $info = $this->store[$object];
                        return $info;
                    }
                }
                break;
            case "object":
                $isset = isset($this->store[$offset]);
                if ($isset)
                {
                    $info = $this->store[$offset];
                    return $info;
                }
                break;
        }
        return null;
    }

    public function offsetUnset (mixed $offset) : void
    {
        $type = gettype ($offset);
        switch ($type)
        {
            case "callable":
                $subset = $this->sub ($offset);
                $count = count ($subset);
                if ($count)
                {
                    $exists = true;
                }
                break;
            case "string":
            case "integer":
                $isset = isset($this->posit[$offset]);
                if ($isset)
                {
                    $isnull = is_null($this->posit[$offset]);
                    if (!$isnull)
                    {
                        $object = $this->posit[$offset];
                        $this->posit[$offset] = null;
                        $this->store->detach($object);
                    }
                }
                break;
            case "object":
                $isset = isset($this->store[$offset]);
                if ($isset)
                {
                    $this->posit[$offset->at] = null;
                    $this->store->detach($offset);
                }
                break;
        }
    }

    public function sub(callable $callback): array
    {
        $subset = [];
        foreach ($this->store as $object)
        {
            $info = $this->store[$object];
            if ($callback($object, $info) === true)
            {
                $subset[] = ["object" => $object, "info" => $info];
            }
        }
        return $subset;
    }

    /**
     * {@inheritdoc}
     */
    public function new(mixed $info): object
    {
        $object = new UUID($this, $this->cardinal);
        $this->store[$object] = $info;
        $this->posit[] = $object;
        ++$this->cardinal;
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function not(mixed $object_or_info): bool
    {
        $filters = [
            fn($object, $info) => recursive_existance($info, $object_or_info),
            fn($object, $info) => recursive_existance($object, $object_or_info)
        ];
        foreach($filters as $filter)
        {
            $subset = $this->sub($filter);
            $count = count($subset);
            if ($count === 1)
            {
                foreach($subset as $set)
                {
                    $object = $set["object"];
                    $this->offsetUnset($object);
                    return true;
                }
            }
        }
        return false;
    }

    public function attach(object &$object, mixed &$info)
    {
        $this->store[$object] = $info;
        $this->posit[] = $object;
        ++$this->cardinal;
    }

    public function detach(object $object)
    {
        $this->store->detach($object);
        foreach($this->posit as $posit)
        {
            if($posit === $object)
            {
                $posit = null;
                break;
            }
        }
    }
}