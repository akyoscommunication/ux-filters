<?php

namespace Akyos\UXFilters\Class;

use Akyos\UXFilters\Class\Fields\ChoiceField;
use Symfony\Component\Translation\TranslatableMessage;

class Choices extends Filter
{
    public array $choices = [];

    public bool $multiple = false;

    public bool $expanded = false;

    public function __construct(string $name, ?string $label = null, string|bool|null $placeholder = null)
    {
        parent::__construct($name, $label, $placeholder);
        $this->type = ChoiceField::class;
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function expanded(bool $expanded = true): self
    {
        $this->expanded = $expanded;
        return $this;
    }

    public function setChoices(array $choices): self
    {
        $this->choices = $choices;

        return $this;
    }

    public function setMultiple(bool $multiple): Choices
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function getOptions(): array
    {
        return $this->options = array_merge($this->options, [
            'expanded' => $this->expanded,
            'choices' => $this->getChoices(),
            'placeholder' => $this->getPlaceholderTransDomain() && $this->getPlaceholder() !== false ? new TranslatableMessage($this->getPlaceholder(), [], $this->getPlaceholderTransDomain()) : $this->getPlaceholder(),
            'multiple' => $this->isMultiple(),
            'choice_attr' => function (mixed $value): array {
                return ['data-model' => 'valuesFilters.' . $this->getName()];
            },
        ]);
    }
}
