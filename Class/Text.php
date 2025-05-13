<?php

namespace Akyos\UXFilters\Class;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class Text extends Filter
{
    private ?string $multipleWord = null;


    public function __construct(string $name, ?string $label = null, ?string $placeholder = null)
    {
        parent::__construct($name, $label, $placeholder);
        $this->type = TextType::class;
    }

    public function setMultipleWord(?string $multipleWord): static
    {
        $this->multipleWord = $multipleWord;

        return $this;
    }
    
    public function getMultipleWord(): ?string
    {
        return $this->multipleWord;
    }
}
