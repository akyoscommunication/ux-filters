# UX Filters Bundle
A Symfony bundle to manage filters in live Component

Sample:

# Php file
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

# Template
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
