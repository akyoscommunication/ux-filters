# UX Filters Bundle

Un bundle Symfony pour gérer facilement les filtres dans vos composants Live.

## Installation

```bash
composer require akyos/ux-filters
```

## Documentation

La documentation complète est disponible dans le dossier `docs/` :

- [Guide d'installation](docs/installation.md)
- [Types de filtres disponibles](docs/filters.md)
- [Configuration](docs/configuration.md)
- [Exemples d'utilisation](docs/examples.md)
- [API Reference](docs/api.md)

## Fonctionnalités principales

- Filtres de texte avec recherche LIKE
- Filtres de sélection (Choices)
- Filtres de date
- Filtres d'entités
- Filtres d'énumération
- Intégration avec la pagination
- Support des composants Live de Symfony

## Exemple rapide

```php
<?php

namespace App\Twig\Components\Product;

use Akyos\UXFilters\Class\Text;
use Akyos\UXFilters\Trait\ComponentWithFilterTrait;
use Akyos\UXPagination\Trait\ComponentWithPaginationTrait;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Index extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;
    use ComponentWithPaginationTrait;

    public function __construct()
    {
        $this->repository = Product::class;
        $this->setLimit(3);
    }

    protected function setFilters(): iterable
    {
        yield (new Text('name', 'Name'))->setSearchType('like')->setParams([
            'entity.name',
            'entity.description',
        ]);
    }
}
```

## Licence

Ce bundle est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

# Template
```html
<div{{ attributes }}>
    {{ form(formFilters) }}
    {% for product in elements %}
        <div class="product">
            <div class="product__content">
                <h3 class="product__title">{{ product.name }}</h3>
                <p class="product__price">{{ product.description|raw }}</p>
                <p class="product__price">{{ product.price }}</p>
            </div>
        </div>
    {% endfor %}
    {{ pagination|raw }}
</div>
```
