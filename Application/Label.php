<?php

class Label implements \Stringable, \ArrayAccess
{
    protected array $labels = [];

    /**
     * Accepts a variable number of strings or arrays to define the Rate.
     * - Strings and items in numeric arrays are added to the public 'labels' list.
     * - Key-value pairs in associative arrays are set as private properties via __set().
     */
    public function __construct(mixed ...$items)
    {
        // Initialize the labels array.
        $this->labels = [];

        // Process each top-level argument passed to the constructor.
        foreach ($items as $item) {
            $this->processArgument($item);
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        foreach ($value as $item) {
            $this->processArgument($item);
        }
    }

    public function in_breadth(string $needle, array $haystack): bool
    {
        foreach ($haystack as $key => $value)
        {
            if (is_string($key) && $key === $needle)
            {
                return true;
            }
            if (is_array ($value))
            {
                $within = $this->in_breadth($needle, $value);
                if ($within)
                {
                    return true;
                }
            }
            elseif (is_string($value) && $value === $needle)
            {
                return true;
            }
            elseif ($value instanceof Label)
            {
                if ($value->contains($needle))
                {
                    return true;
                }
            }
        }
        return false;
    }

    public function contains(mixed $value): bool
    {
        return $this->in_breadth($value, $this->labels);
    }

    public function add(string|Label $value): void {
        $this->labels[] = $value;
    }

    public function offsetExists(mixed $offset): bool
    {
        return Label::in_breadth($offset, $this->labels);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!isset($this->labels[$offset]))
        {
            if (is_numeric($offset))
            {
                if (count($this->labels) > $offset)
                {
                    foreach($this->labels as $key => $value)
                    {
                        --$offset;
                        if ($offset < 0)
                        {
                            return $key;
                        }
                    }
                }
            }
        }
        if (isset($this->labels[$offset]))
        {
            return $this->labels[$offset];
        }
        throw new OutOfBoundsException();
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->labels = array_splice($this->labels, $offset, 1);
    }

    /**
     * Recursively processes an argument to populate labels or properties.
     *
     * @param mixed $argument The item to process.
     */
    private function processArgument(mixed $argument): void
    {
        // Case 1: The argument is a simple string.
        if (is_string($argument)) {
            // Normalize and add it to the public labels list.
            $this->labels[] = Label::normalize($argument);
            return;
        }

        // Case 2: The argument is an array.
        if (is_array($argument)) {
            // Determine if the array is associative (string-keyed) or numeric (list-style).
            // An array is considered associative if its keys are not a simple 0-based integer sequence.
            if (count($argument) > 0 && array_keys($argument) !== range(0, count($argument) - 1)) {
                // It's an associative array. Process each key-value pair.
                foreach ($argument as $key => $value) {
                    // Per the requirement, we only act on string keys.
                    if (is_string($key)) {
                        $normalizedKey = Label::normalize($key);
                        // The entire value structure must be normalized before being set.
                        $normalizedValue = $this->recursivelyNormalize($value);
                        $this->__set($normalizedKey, new Label($normalizedValue));
                    }
                }
            } else {
                // It's a numeric array (a list). Process each of its items individually.
                foreach ($argument as $value) {
                    $this->processArgument($value); // Recursive call
                }
            }
        } elseif ($argument instanceof Label) {
            // Case 3: The argument is an instance of Label.
            $this->labels[] = $argument;
        }
        // Any other data types (integers, booleans, etc.) are ignored.
    }

    /**
     * Recursively traverses a data structure, normalizing any string keys and string values.
     *
     * @param mixed $data The data to normalize.
     * @return mixed The fully normalized data.
     */
    private function recursivelyNormalize(mixed $data): mixed
    {
        // If the data is a string, normalize it and return.
        if (is_string($data)) {
            return Label::normalize($data);
        }

        // If it's not an array, we can't recurse further, so return it as-is.
        if (!is_array($data)) {
            return $data;
        }

        $normalizedArray = [];
        foreach ($data as $key => $value) {
            // Normalize the key if it's a string, otherwise use the integer key.
            $newKey = is_string($key) ? Label::normalize($key) : $key;
            // Recurse into the value to normalize it.
            $newValue = $this->recursivelyNormalize($value);
            $normalizedArray[$newKey] = $newValue;
        }

        return ($normalizedArray);
    }

    function __set(?string $label, mixed $labels = null) : void
    {
        if (is_array($labels))
        {
            foreach($labels as &$labe)
            {
                $labe = Label::normalize($labe);
            }
            $this->labels[$label] = $labels;
        }
        elseif (is_string($labels))
        {
            $labels = Label::normalize($labels);
            $this->labels[$label] = $labels;
        }
        elseif ($labels instanceof Label)
        {
            $this->labels[$label] = $labels;
        }
    }

    function __get(?string $label) : mixed
    {
        if (isset($this->labels[$label]))
        {
            return $this->labels[$label];
        }
        return null;
    }

    static function normalize(?string $label = null) : string
    {
        return $label;
    }

    function __toString () : string
    {
        $strings = [];
        foreach ($this->labels as $label => $labels)
        {
            if (is_string($label) && !empty($label))
            {
                
                $strings[] = $label;
                $strings[] = SUCH_THAT_SEPARATOR;
            }
            if (is_array($labels))
            {
                foreach ($labels as $alias)
                {
                    $strings[] = $alias;
                    $strings[] = LIST_SEPARATOR;
                }
                array_pop($strings);
            }
            else
            {
                $strings[] = $labels;
                $strings[] = LIST_SEPARATOR;
            }
        }
        $A = join ("", $strings);
        $A = rtrim ($A, LIST_SEPARATOR . SUCH_THAT_SEPARATOR);
        if (empty($A))
        {
            return "";
        }
        $A = "{$A}";
        return $A;
    }

    static function fromString (string $from)
    {
        $pattern = '/
            (?<primary>[^\s\\' . SUCH_THAT_SEPARATOR . LIST_SEPARATOR . ']+)              # Primary token
            (?:\s*\\' . SUCH_THAT_SEPARATOR . '\s*                        # Optional colon
                (?<secondary>[^\\' . SUCH_THAT_SEPARATOR . LIST_SEPARATOR . ']+(?:\s*' . LIST_SEPARATOR . '\s*[^\\' . SUCH_THAT_SEPARATOR . LIST_SEPARATOR . ']+)*) # Secondary tokens
            )?
            (?=\s|' . LIST_SEPARATOR . '|$)                        # Lookahead for separator or end
        /x';
        preg_match_all($pattern, $from, $matches, PREG_SET_ORDER);
        $result = [];
        foreach ($matches as $match) {
            $primary = $match['primary'];
            $secondary = [];

            if (!empty($match['secondary'])) {
                $secondary = preg_split('/\s*,\s*/', $match['secondary']);
            }

            $result[$primary] = $secondary;
        }
        return new Label($result);
    }
}