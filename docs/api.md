# API Reference

## Navigation

- [Guide d'installation](installation.md)
- [Types de filtres disponibles](filters.md)
- [Exemples d'utilisation](examples.md)
- [API Reference](api.md) (page actuelle)

## Trait ComponentWithFilterTrait

Le trait `ComponentWithFilterTrait` fournit les fonctionnalités de filtrage pour vos composants Live.

### Propriétés

- `public array $valuesFilters = []` : Stocke les valeurs des filtres
- `protected ?string $repository = null` : Le repository à utiliser pour les requêtes
- `protected ?EntityManagerInterface $entityManager = null` : L'EntityManager pour les requêtes
- `protected ?FormInterface $formFilters = null` : Le formulaire de filtres
- `protected ?array $filters = null` : Les filtres configurés

### Méthodes principales

#### `getDefaultData(): QueryBuilder|array|null`
```php
public function getDefaultData(): QueryBuilder|array|null
```
Retourne les données source sur lesquelles appliquer les filtres. Peut retourner :
- Un QueryBuilder personnalisé
- Un tableau de données
- `null` pour utiliser le QueryBuilder par défaut

#### `setFilters(): iterable`
```php
protected function setFilters(): iterable
```
Méthode à implémenter pour définir les filtres du composant. Doit retourner un iterable de filtres.

#### `getData(): array`
```php
public function getData(): array
```
Récupère les données filtrées. Gère automatiquement :
- La construction du formulaire de filtres
- L'application des filtres sur les données
- Le retour des résultats

#### `addSearchQuery(QueryBuilder $queryBuilder, array $values): void`
```php
protected function addSearchQuery(QueryBuilder $queryBuilder, array $values): void
```
Applique les conditions de filtrage sur le QueryBuilder en fonction des valeurs des filtres.

## Classe Filter

Classe de base pour tous les filtres.

### Propriétés

- `public string $name` : Le nom du filtre
- `public string $label` : Le label du filtre
- `public string|bool|null $placeholder` : Le placeholder du filtre
- `public ?string $transDomain = null` : Le domaine de traduction pour le label
- `public ?string $placeholderTransDomain = null` : Le domaine de traduction pour le placeholder
- `public array $options = []` : Les options du filtre
- `public string $searchType = 'eq'` : Le type de recherche
- `public mixed $defaultValue = null` : La valeur par défaut du filtre
- `public array $params = []` : Les champs sur lesquels appliquer le filtre
- `public ?string $type = null` : Le type de champ de formulaire
- `public string $classCSS = ''` : La classe CSS du conteneur

### Méthodes de configuration

#### `setSearchType(string $searchType): self`
```php
public function setSearchType(string $searchType): self
```
Définit le type de recherche pour le filtre.

#### `setParams(array $params): self`
```php
public function setParams(array $params): self
```
Définit les champs sur lesquels appliquer le filtre.

#### `addParam(string|array $params): self`
```php
public function addParam(string|array $params): self
```
Ajoute un ou plusieurs champs au filtre.

#### `setPlaceholder(string|bool|null $placeholder): self`
```php
public function setPlaceholder(string|bool|null $placeholder): self
```
Définit le placeholder du champ de formulaire.

#### `setClassCSS(string $classCss): self`
```php
public function setClassCSS(string $classCss): self
```
Définit la classe CSS pour le conteneur du filtre.

#### `setTransDomain(?string $transDomain): self`
```php
public function setTransDomain(?string $transDomain): self
```
Définit le domaine de traduction pour le label.

#### `setPlaceholderTransDomain(?string $placeholderTransDomain): self`
```php
public function setPlaceholderTransDomain(?string $placeholderTransDomain): self
```
Définit le domaine de traduction pour le placeholder.

#### `setDefaultValue(mixed $defaultValue): self`
```php
public function setDefaultValue(mixed $defaultValue): self
```
Définit la valeur par défaut du filtre.

### Méthodes d'options

#### `addOption(string $name, array|string|\Closure $value): self`
```php
public function addOption(string $name, array|string|\Closure $value): self
```
Ajoute une option au filtre. Les options courantes sont :
- `label` : Le label du champ
- `required` : Si le champ est requis
- `help` : Le texte d'aide
- `attr` : Les attributs HTML

#### `addOptions(array $options): self`
```php
public function addOptions(array $options): self
```
Ajoute plusieurs options au filtre.

#### `setOptions(array $options): self`
```php
public function setOptions(array $options): self
```
Définit toutes les options du filtre.

### Méthodes de récupération

#### `getName(): string`
```php
public function getName(): string
```
Récupère le nom du filtre.

#### `getLabel(): string`
```php
public function getLabel(): string
```
Récupère le label du filtre.

#### `getParams(): array`
```php
public function getParams(): array
```
Récupère les champs sur lesquels le filtre est appliqué.

#### `getType(): ?string`
```php
public function getType(): ?string
```
Récupère le type de champ de formulaire.

#### `getPlaceholder(): string|bool|null`
```php
public function getPlaceholder(): string|bool|null
```
Récupère le placeholder du filtre.

#### `getSearchType(): string`
```php
public function getSearchType(): string
```
Récupère le type de recherche.

#### `getOptions(): array`
```php
public function getOptions(): array
```
Récupère toutes les options du filtre.

#### `getClassCSS(): string`
```php
public function getClassCSS(): string
```
Récupère la classe CSS du conteneur.

#### `getTransDomain(): ?string`
```php
public function getTransDomain(): ?string
```
Récupère le domaine de traduction du label.

#### `getPlaceholderTransDomain(): ?string`
```php
public function getPlaceholderTransDomain(): ?string
```
Récupère le domaine de traduction du placeholder.

#### `getDefaultValue(): mixed`
```php
public function getDefaultValue(): mixed
```
Récupère la valeur par défaut du filtre.

## Types de filtres disponibles

### Text

Filtre de type texte avec recherche LIKE.

#### Méthodes spécifiques

- `setMultipleWord(?string $multipleWord): self` : Définit le mode de recherche multiple ('and' ou 'or')
- `getMultipleWord(): ?string` : Récupère le mode de recherche multiple

```php
use Akyos\UXFilters\Class\Text;

$filter = new Text('name', 'Nom');
$filter->setSearchType('like')
    ->setParams(['entity.name', 'entity.description'])
    ->setPlaceholder('Rechercher...')
    ->setMultipleWord('and'); // Recherche avec AND entre les mots
```

### Choices

Filtre de type choix avec options prédéfinies.

#### Méthodes spécifiques

- `setChoices(array $choices): self` : Définit la liste des choix disponibles
- `getChoices(): array` : Récupère la liste des choix
- `setMultiple(bool $multiple): self` : Définit si la sélection multiple est autorisée
- `isMultiple(): bool` : Vérifie si la sélection multiple est autorisée
- `expanded(bool $expanded = true): self` : Définit si les choix sont affichés en radio/checkbox

```php
use Akyos\UXFilters\Class\Choices;

$filter = new Choices('status', 'Statut');
$filter->setChoices([
    'active' => 'Actif',
    'inactive' => 'Inactif',
])
->setParams(['entity.status'])
->setMultiple(true)
->expanded(true);
```

### Date

Filtre de type date.

```php
use Akyos\UXFilters\Class\Date;

$filter = new Date('createdAt', 'Date de création');
$filter->setParams(['entity.createdAt'])
    ->addOption('widget', 'single_text');
```

### Entity

Filtre de type entité avec relation.

#### Méthodes spécifiques

- `setClass(string $class): self` : Définit la classe de l'entité
- `getClass(): string` : Récupère la classe de l'entité
- `setQueryBuilder(?Closure $repository = null): self` : Définit le QueryBuilder personnalisé
- `getQueryBuilder(): ?Closure` : Récupère le QueryBuilder personnalisé
- `setMultiple(bool $multiple): self` : Définit si la sélection multiple est autorisée
- `isMultiple(): bool` : Vérifie si la sélection multiple est autorisée
- `setChoiceLabel(string|Closure $choiceLabel): self` : Définit le label des choix

```php
use Akyos\UXFilters\Class\Entity;

$filter = new Entity('category', 'Catégorie');
$filter->setClass(Category::class)
    ->setProperty('name')
    ->setParams(['entity.category'])
    ->setMultiple(true)
    ->setQueryBuilder(function($qb) {
        return $qb->orderBy('c.name', 'ASC');
    });
```

### Enum

Filtre de type énumération.

#### Méthodes spécifiques

- `setClass(string $class): self` : Définit la classe de l'énumération
- `getClass(): string` : Récupère la classe de l'énumération
- `setMultiple(bool $multiple): self` : Définit si la sélection multiple est autorisée
- `isMultiple(): bool` : Vérifie si la sélection multiple est autorisée

```php
use Akyos\UXFilters\Class\Enum;

$filter = new Enum('type', 'Type');
$filter->setClass(ProductType::class)
    ->setParams(['entity.type'])
    ->setMultiple(true);
```

## Utilisation dans un composant Live

```php
use Akyos\UXFilters\Trait\ComponentWithFilterTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent]
final class ProductList
{
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

## Template Twig

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