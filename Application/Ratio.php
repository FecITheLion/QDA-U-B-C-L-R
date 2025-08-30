<?php

class Ratio
{
    protected array $components;
    protected array $labels;
    protected Label $label;

    private function __construct (array $components)
    { 
        $this->label = new Label();
        $this->components = $components;
    }

    public function with (array $some): self
    {
        foreach ($some as $one)
        {
            if ($one instanceof Label)
            {
                $this->labels[] = $one;
            }
            elseif (is_string ($one))
            {
                $this->label[] = $one;
            }
        }
        return $this;
    }
}