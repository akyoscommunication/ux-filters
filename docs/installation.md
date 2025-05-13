# Guide d'installation

## Navigation

- [Guide d'installation](installation.md) (page actuelle)
- [Types de filtres disponibles](filters.md)
- [Exemples d'utilisation](examples.md)
- [API Reference](api.md)

## Prérequis

- PHP 8.1 ou supérieur
- Symfony 6.0 ou supérieur
- Symfony UX Live Components

## Installation

1. Installez le bundle via Composer :

```bash
composer require akyos/ux-filters
```

2. Le bundle s'auto-configurera automatiquement. Si vous avez désactivé l'auto-configuration, ajoutez manuellement le bundle dans `config/bundles.php` :

```php
return [
    // ...
    Akyos\UXFilters\UXFilters::class => ['all' => true],
];
```

## Utilisation dans un composant Live

1. Créez votre composant Live :

```php
<?php

namespace App\Twig\Components\Product;

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
}
```

2. Implémentez la méthode `setFilters()` qui est obligatoire. Cette méthode doit retourner un `iterable` de filtres. Vous pouvez utiliser `yield` pour retourner chaque filtre :

```php
protected function setFilters(): iterable
{
    // Filtre de texte avec recherche LIKE
    yield (new Text('search', 'Rechercher'))
        ->setSearchType('like')
        ->setParams(['entity.name', 'entity.description'])
        ->setPlaceholder('Rechercher un produit...')
        ->addOption('help', 'Aide à la recherche')
        ->addOption('attr', ['class' => 'custom-input']);

    // Filtre de sélection avec des choix prédéfinis
    yield (new Choices('status', 'Statut'))
        ->setChoices([
            'active' => 'Actif',
            'inactive' => 'Inactif',
        ])
        ->setMultiple(true)
        ->addOption('help', 'Sélectionnez le statut');

    // Filtre de date
    yield (new Date('createdAt', 'Date de création'))
        ->addOption('help', 'Filtrer par date de création');

    // Filtre d'entité (relation)
    yield (new Entity('category', 'Catégorie'))
        ->setClass(Category::class)
        ->setProperty('name')
        ->addOption('attr', ['class' => 'custom-select']);

    // Filtre d'énumération
    yield (new Enum('type', 'Type'))
        ->setEnum(ProductType::class)
        ->addOption('help', 'Sélectionnez le type');
}
```

Les types de filtres disponibles sont :

- `Text` : Pour les champs de texte avec recherche LIKE
- `Choices` : Pour les sélections avec des choix prédéfinis
- `Date` : Pour les champs de date
- `Entity` : Pour les relations avec d'autres entités
- `Enum` : Pour les champs de type énumération

Chaque filtre peut être configuré avec des options spécifiques via la méthode `addOption()` :

```php
// Exemple avec un filtre de texte
yield (new Text('search', 'Rechercher'))
    ->setSearchType('like')                    // Type de recherche
    ->setParams(['entity.name'])               // Champs sur lesquels chercher
    ->setPlaceholder('Rechercher...')          // Placeholder du champ
    ->addOption('required', false)             // Champ requis ou non
    ->addOption('help', 'Aide à la recherche') // Texte d'aide
    ->addOption('attr', ['class' => 'custom-input']); // Attributs HTML

// Exemple avec un filtre de sélection
yield (new Choices('status', 'Statut'))
    ->setChoices([                             // Liste des choix
        'active' => 'Actif',
        'inactive' => 'Inactif',
    ])
    ->setMultiple(true)                        // Sélection multiple
    ->addOption('expanded', true)              // Afficher en radio/checkbox
    ->addOption('required', true);             // Champ requis
```

3. Créez le template Twig correspondant :

```twig
<div{{ attributes }}>
    {{ form(formFilters) }}
    {% for product in elements %}
        <div class="product">
            <h3>{{ product.name }}</h3>
            <p>{{ product.description }}</p>
        </div>
    {% endfor %}
</div>
```

## Prochaines étapes

- Consultez la [documentation des types de filtres](filters.md) pour découvrir les différents filtres disponibles
- Découvrez des [exemples d'utilisation](examples.md) plus avancés
- Consultez la [référence API](api.md) pour plus de détails sur les classes et méthodes disponibles 