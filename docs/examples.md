# Exemples d'utilisation

## Navigation

- [Guide d'installation](installation.md)
- [Types de filtres disponibles](filters.md)
- [Exemples d'utilisation](examples.md) (page actuelle)
- [API Reference](api.md)

## Exemple 1 : Liste de produits avec recherche et filtres

```php
<?php

namespace App\Twig\Components\Product;

use Akyos\UXFilters\Class\Text;
use Akyos\UXFilters\Class\Choices;
use Akyos\UXFilters\Class\Entity;
use Akyos\UXFilters\Trait\ComponentWithFilterTrait;
use Akyos\UXPagination\Trait\ComponentWithPaginationTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ProductList
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;
    use ComponentWithPaginationTrait;

    public function __construct()
    {
        $this->repository = Product::class;
        $this->setLimit(12);
    }

    protected function setFilters(): iterable
    {
        yield (new Text('search', 'Rechercher'))
            ->setSearchType('like')
            ->setParams(['entity.name', 'entity.description'])
            ->setPlaceholder('Rechercher un produit...');

        yield (new Choices('status', 'Statut'))
            ->setChoices([
                'active' => 'Actif',
                'inactive' => 'Inactif',
            ]);

        yield (new Entity('category', 'Catégorie'))
            ->setClass(Category::class)
            ->setProperty('name');
    }
}
```

Template Twig correspondant :

```twig
<div{{ attributes }}>
    <div class="filters">
        {{ form(formFilters) }}
    </div>

    <div class="products-grid">
        {% for product in elements %}
            <div class="product-card">
                <h3>{{ product.name }}</h3>
                <p>{{ product.description }}</p>
                <p class="price">{{ product.price|number_format(2, ',', ' ') }} €</p>
                <p class="category">{{ product.category.name }}</p>
            </div>
        {% endfor %}
    </div>

    {{ pagination|raw }}
</div>
```

## Exemple 2 : Liste d'articles avec filtres avancés

```php
<?php

namespace App\Twig\Components\Article;

use Akyos\UXFilters\Class\Text;
use Akyos\UXFilters\Class\Date;
use Akyos\UXFilters\Class\Enum;
use Akyos\UXFilters\Trait\ComponentWithFilterTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ArticleList
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;

    public function __construct()
    {
        $this->repository = Article::class;
    }

    protected function setFilters(): iterable
    {
        yield (new Text('search', 'Rechercher'))
            ->setSearchType('like')
            ->setParams(['entity.title', 'entity.content'])
            ->setPlaceholder('Rechercher un article...');

        yield (new Date('publishedAt', 'Date de publication'))
            ->setHelp('Filtrer par date de publication');

        yield (new Enum('status', 'Statut'))
            ->setEnum(ArticleStatus::class)
            ->setRequired(true);
    }
}
```

## Exemple 3 : Liste d'utilisateurs avec filtres personnalisés

```php
<?php

namespace App\Twig\Components\User;

use Akyos\UXFilters\Class\Text;
use Akyos\UXFilters\Class\Choices;
use Akyos\UXFilters\Class\Entity;
use Akyos\UXFilters\Trait\ComponentWithFilterTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class UserList
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;

    public function __construct()
    {
        $this->repository = User::class;
    }

    protected function setFilters(): iterable
    {
        yield (new Text('search', 'Rechercher'))
            ->setSearchType('like')
            ->setParams(['entity.firstName', 'entity.lastName', 'entity.email'])
            ->setPlaceholder('Rechercher un utilisateur...');

        yield (new Choices('role', 'Rôle'))
            ->setChoices([
                'ROLE_USER' => 'Utilisateur',
                'ROLE_ADMIN' => 'Administrateur',
                'ROLE_SUPER_ADMIN' => 'Super Administrateur',
            ]);

        yield (new Entity('department', 'Département'))
            ->setClass(Department::class)
            ->setProperty('name')
            ->setAttr(['class' => 'custom-select']);
    }
}
```

## Rendu des filtres

Les filtres sont rendus directement dans votre template de composant via la fonction `form()` de Twig :

```twig
<div{{ attributes }}>
    {{ form(formFilters) }}
    
    {# Votre contenu ici #}
</div>
```

Le rendu utilise le système de formulaires de Symfony, vous pouvez donc :

1. Personnaliser le style en utilisant les classes CSS de Symfony Form
2. Surcharger les templates de formulaire de Symfony pour personnaliser le rendu
3. Utiliser les attributs HTML via la méthode `addOption('attr', [...])` sur vos filtres

Exemple de personnalisation des attributs :

```php
yield (new Text('search', 'Rechercher'))
    ->addOption('attr', [
        'class' => 'custom-input',
        'data-custom' => 'value'
    ]);
```

Pour plus d'informations sur la personnalisation des formulaires Symfony, consultez la [documentation officielle](https://symfony.com/doc/current/form/form_customization.html).

## Récupération des valeurs filtrées

Le trait `ComponentWithFilterTrait` gère automatiquement le filtrage des données. Voici comment cela fonctionne :

1. Les valeurs des filtres sont automatiquement stockées dans la propriété `valuesFilters` du composant
2. La méthode `getData()` du trait gère automatiquement le filtrage des données
3. Les filtres sont appliqués via la méthode `addSearchQuery()` qui construit la requête en fonction des valeurs des filtres

Exemple de composant avec filtrage automatique :

```php
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
            ->setParams(['entity.name', 'entity.description']);

        yield (new Choices('status', 'Statut'))
            ->setChoices([
                'active' => 'Actif',
                'inactive' => 'Inactif',
            ])
            ->setParams(['entity.status']);
    }
}
```

Dans votre template Twig, vous pouvez accéder aux données filtrées via la variable `elements` :

```twig
<div{{ attributes }}>
    {{ form(formFilters) }}
    
    {# Affichage des valeurs actuelles des filtres #}
    <div class="active-filters">
        {% if valuesFilters.search %}
            <span>Recherche : {{ valuesFilters.search }}</span>
        {% endif %}
        {% if valuesFilters.status %}
            <span>Statut : {{ valuesFilters.status }}</span>
        {% endif %}
    </div>

    {# Liste des éléments filtrés #}
    {% for product in elements %}
        <div class="product">
            <h3>{{ product.name }}</h3>
            <p>{{ product.description }}</p>
        </div>
    {% endfor %}
</div>
```

Le trait gère automatiquement :
- La mise à jour des valeurs des filtres
- La construction de la requête de filtrage
- L'application des conditions sur les champs spécifiés
- La gestion des différents types de recherche (LIKE, exact, etc.) 

## Personnalisation des données par défaut

Vous pouvez personnaliser les données par défaut en surchargeant la méthode `getDefaultData()`. Voici quelques exemples :

### Exemple 1 : Filtrage sur un tableau de données

```php
#[AsLiveComponent]
final class ProductList
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;

    public function __construct()
    {
        $this->repository = Product::class;
    }

    public function getDefaultData(): array
    {
        // Retourne un tableau de produits à filtrer
        return [
            ['name' => 'Produit 1', 'description' => 'Description 1', 'status' => 'active'],
            ['name' => 'Produit 2', 'description' => 'Description 2', 'status' => 'inactive'],
            ['name' => 'Produit 3', 'description' => 'Description 3', 'status' => 'active'],
        ];
    }

    protected function setFilters(): iterable
    {
        yield (new Text('search', 'Rechercher'))
            ->setSearchType('like')
            ->setParams(['name', 'description']);

        yield (new Choices('status', 'Statut'))
            ->setChoices([
                'active' => 'Actif',
                'inactive' => 'Inactif',
            ])
            ->setParams(['status']);
    }
}
```

### Exemple 2 : Filtrage sur les posts de l'utilisateur connecté

```php
#[AsLiveComponent]
final class UserPosts
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;

    public function __construct(
        private Security $security
    ) {
        $this->repository = Post::class;
    }

    public function getDefaultData(): QueryBuilder
    {
        // Récupère l'utilisateur connecté
        $user = $this->security->getUser();
        
        // Crée un QueryBuilder personnalisé pour ne récupérer que les posts de l'utilisateur
        return $this->entityManager->getRepository(Post::class)
            ->createQueryBuilder('p')
            ->where('p.author = :user')
            ->setParameter('user', $user);
    }

    protected function setFilters(): iterable
    {
        yield (new Text('search', 'Rechercher'))
            ->setSearchType('like')
            ->setParams(['p.title', 'p.content']);

        yield (new Date('publishedAt', 'Date de publication'))
            ->setParams(['p.publishedAt']);

        yield (new Choices('status', 'Statut'))
            ->setChoices([
                'draft' => 'Brouillon',
                'published' => 'Publié',
                'archived' => 'Archivé',
            ])
            ->setParams(['p.status']);
    }
}
```

### Exemple 3 : Filtrage sur des données avec des relations

```php
#[AsLiveComponent]
final class CategoryProducts
{
    use DefaultActionTrait;
    use ComponentWithFilterTrait;

    public function __construct()
    {
        $this->repository = Product::class;
    }

    public function getDefaultData(): QueryBuilder
    {
        // Crée un QueryBuilder avec des jointures pour optimiser les requêtes
        return $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.tags', 't')
            ->addSelect('c', 't');
    }

    protected function setFilters(): iterable
    {
        yield (new Text('search', 'Rechercher'))
            ->setSearchType('like')
            ->setParams(['p.name', 'p.description', 'c.name', 't.name']);

        yield (new Entity('category', 'Catégorie'))
            ->setClass(Category::class)
            ->setChoiceLabel('name')
            ->setParams(['p.category']);

        yield (new Entity('tags', 'Tags'))
            ->setClass(Tag::class)
            ->setChoiceLabel('name')
            ->setMultiple(true)
            ->setParams(['t.id']);
    }
}
```

Dans ces exemples :
1. `getDefaultData()` peut retourner :
   - Un tableau de données à filtrer
   - Un QueryBuilder personnalisé
   - `null` pour utiliser le QueryBuilder par défaut
2. Les filtres sont appliqués sur les données retournées
3. Vous pouvez combiner le filtrage avec d'autres conditions (jointures, where, etc.)
4. Les paramètres des filtres doivent correspondre aux champs disponibles dans les données

## Récupération des valeurs filtrées

Le trait `ComponentWithFilterTrait` gère automatiquement le filtrage des données. Voici comment cela fonctionne :

1. Les valeurs des filtres sont automatiquement stockées dans la propriété `valuesFilters` du composant
2. La méthode `getData()` du trait gère automatiquement le filtrage des données
3. Les filtres sont appliqués via la méthode `addSearchQuery()` qui construit la requête en fonction des valeurs des filtres

Exemple de composant avec filtrage automatique :

```php
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
            ->setParams(['entity.name', 'entity.description']);

        yield (new Choices('status', 'Statut'))
            ->setChoices([
                'active' => 'Actif',
                'inactive' => 'Inactif',
            ])
            ->setParams(['entity.status']);
    }
}
```

Dans votre template Twig, vous pouvez accéder aux données filtrées via la variable `elements` :

```twig
<div{{ attributes }}>
    {{ form(formFilters) }}
    
    {# Affichage des valeurs actuelles des filtres #}
    <div class="active-filters">
        {% if valuesFilters.search %}
            <span>Recherche : {{ valuesFilters.search }}</span>
        {% endif %}
        {% if valuesFilters.status %}
            <span>Statut : {{ valuesFilters.status }}</span>
        {% endif %}
    </div>

    {# Liste des éléments filtrés #}
    {% for product in elements %}
        <div class="product">
            <h3>{{ product.name }}</h3>
            <p>{{ product.description }}</p>
        </div>
    {% endfor %}
</div>
```

Le trait gère automatiquement :
- La mise à jour des valeurs des filtres
- La construction de la requête de filtrage
- L'application des conditions sur les champs spécifiés
- La gestion des différents types de recherche (LIKE, exact, etc.) 