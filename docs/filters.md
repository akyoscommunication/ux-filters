# Types de filtres disponibles

## Navigation

- [Guide d'installation](installation.md)
- [Types de filtres disponibles](filters.md) (page actuelle)
- [Exemples d'utilisation](examples.md)
- [API Reference](api.md)

Le bundle UX Filters propose plusieurs types de filtres pour répondre à vos besoins. Voici une description détaillée de chacun d'eux.

## Filtre de texte (Text)

Le filtre de texte permet de rechercher dans un ou plusieurs champs avec une recherche LIKE.

### Méthodes spécifiques

- `setMultipleWord(?string $multipleWord)`: Définit le mode de recherche multiple ('and' ou 'or')
- `getMultipleWord()`: Retourne le mode de recherche multiple

```php
use Akyos\UXFilters\Class\Text;

protected function setFilters(): iterable
{
    yield (new Text('search', 'Rechercher'))
        ->setSearchType('like')
        ->setParams([
            'entity.name',
            'entity.description',
        ])
        ->setMultipleWord('and'); // Recherche avec AND entre les mots
}
```

## Filtre de sélection (Choices)

Le filtre de sélection permet de filtrer sur des valeurs prédéfinies.

### Méthodes spécifiques

- `setChoices(array $choices)`: Définit la liste des choix disponibles
- `getChoices()`: Retourne la liste des choix
- `setMultiple(bool $multiple)`: Définit si la sélection multiple est autorisée
- `isMultiple()`: Vérifie si la sélection multiple est autorisée
- `expanded(bool $expanded = true)`: Définit si les choix sont affichés en radio/checkbox

```php
use Akyos\UXFilters\Class\Choices;

protected function setFilters(): iterable
{
    yield (new Choices('status', 'Statut'))
        ->setChoices([
            'active' => 'Actif',
            'inactive' => 'Inactif',
        ])
        ->setMultiple(true)
        ->expanded(true)
        ->setParams([
            'entity.status'
        ]);
}
```

## Filtre de date (Date)

Le filtre de date permet de filtrer sur des champs de type date.

```php
use Akyos\UXFilters\Class\Date;

protected function setFilters(): iterable
{
    yield (new Date('createdAt', 'Date de création'))
        ->setParams([
            'entity.createdAt'
        ]);
}
```

## Filtre d'entité (Entity)

Le filtre d'entité permet de filtrer sur des relations avec d'autres entités.

### Méthodes spécifiques

- `setClass(string $class)`: Définit la classe de l'entité
- `getClass()`: Retourne la classe de l'entité
- `setQueryBuilder(?Closure $repository)`: Définit un QueryBuilder personnalisé
- `getQueryBuilder()`: Retourne le QueryBuilder personnalisé
- `setMultiple(bool $multiple)`: Définit si la sélection multiple est autorisée
- `isMultiple()`: Vérifie si la sélection multiple est autorisée
- `setChoiceLabel(string|Closure $choiceLabel)`: Définit le label à afficher pour chaque choix

```php
use Akyos\UXFilters\Class\Entity;

protected function setFilters(): iterable
{
    yield (new Entity('category', 'Catégorie'))
        ->setClass(Category::class)
        ->setChoiceLabel('name')
        ->setMultiple(true)
        ->setQueryBuilder(function($repository) {
            return $repository->createQueryBuilder('c')
                ->orderBy('c.name', 'ASC');
        })
        ->setParams([
            'entity.category'
        ]);
}
```

## Filtre d'énumération (Enum)

Le filtre d'énumération permet de filtrer sur des champs de type enum.

### Méthodes spécifiques

- `setClass(string $class)`: Définit la classe d'énumération
- `getClass()`: Retourne la classe d'énumération
- `setMultiple(bool $multiple)`: Définit si la sélection multiple est autorisée
- `isMultiple()`: Vérifie si la sélection multiple est autorisée

```php
use Akyos\UXFilters\Class\Enum;

protected function setFilters(): iterable
{
    yield (new Enum('type', 'Type'))
        ->setClass(ProductType::class)
        ->setMultiple(true)
        ->setParams([
            'entity.type'
        ]);
}
```

## Options communes à tous les filtres

Tous les filtres héritent de la classe `Filter` qui fournit les méthodes suivantes :

### Méthodes de configuration

- `setParams(array $params)`: Définit les champs sur lesquels appliquer le filtre
- `addParam(string|array $params)`: Ajoute un ou plusieurs champs au filtre
- `setSearchType(string $type)`: Définit le type de recherche ('like', 'eq', etc.)
- `setPlaceholder(string|bool|null $placeholder)`: Définit le placeholder du champ
- `setClassCSS(string $classCss)`: Définit la classe CSS pour le conteneur du filtre
- `setTransDomain(?string $transDomain)`: Définit le domaine de traduction pour le label
- `setPlaceholderTransDomain(?string $transDomain)`: Définit le domaine de traduction pour le placeholder
- `setDefaultValue(mixed $value)`: Définit la valeur par défaut du filtre

### Méthodes d'options

- `addOption(string $name, mixed $value)`: Ajoute une option au filtre
- `addOptions(array $options)`: Ajoute plusieurs options au filtre
- `setOptions(array $options)`: Définit toutes les options du filtre

### Méthodes de récupération

- `getName()`: Retourne le nom du filtre
- `getLabel()`: Retourne le label du filtre
- `getParams()`: Retourne les champs sur lesquels le filtre est appliqué
- `getType()`: Retourne le type du filtre
- `getPlaceholder()`: Retourne le placeholder du filtre
- `getSearchType()`: Retourne le type de recherche
- `getOptions()`: Retourne toutes les options du filtre
- `getClassCSS()`: Retourne la classe CSS du conteneur
- `getTransDomain()`: Retourne le domaine de traduction du label
- `getPlaceholderTransDomain()`: Retourne le domaine de traduction du placeholder
- `getDefaultValue()`: Retourne la valeur par défaut du filtre

## Exemple complet

```php
<?php

namespace App\Twig\Components\Product;

use Akyos\UXFilters\Class\Text;
use Akyos\UXFilters\Class\Choices;
use Akyos\UXFilters\Class\Date;
use Akyos\UXFilters\Class\Entity;
use Akyos\UXFilters\Trait\ComponentWithFilterTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ProductList
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;

    public function __construct()
    {
        $this->repository = Product::class;
    }

    protected function setFilters(): iterable
    {
        yield (new Text('search', 'Rechercher'))
            ->setSearchType('like')
            ->setParams(['entity.name', 'entity.description'])
            ->setPlaceholder('Rechercher un produit...')
            ->addOption('help', 'Aide à la recherche');

        yield (new Choices('status', 'Statut'))
            ->setChoices([
                'active' => 'Actif',
                'inactive' => 'Inactif',
            ])
            ->setParams(['entity.status'])
            ->addOption('required', false);

        yield (new Date('createdAt', 'Date de création'))
            ->setParams(['entity.createdAt'])
            ->addOption('help', 'Filtrer par date de création');

        yield (new Entity('category', 'Catégorie'))
            ->setClass(Category::class)
            ->setProperty('name')
            ->setParams(['entity.category'])
            ->addOption('attr', ['class' => 'custom-select']);
    }
} 