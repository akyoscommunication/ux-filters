<?php

namespace Akyos\UXFilters\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

trait ComponentWithFilterTrait
{
    use ComponentToolsTrait;

    #[ExposeInTemplate(name: 'formFilters', getter: 'getFormFilters')]
    private ?string $formFilters = null;

    private ?array $filters = null;

    public ?string $repository = null;

    #[LiveProp(writable: true)]
    public string $defaultTransDomain = 'form';

    public EntityManagerInterface $entityManager;
    public FormFactoryInterface $formBuilder;

    public RequestStack $requestFilter;


    #[LiveProp(writable: true, url: true)]
    public array $valuesFilters = [];

    /**
     * @internal
     */
    #[Required]
    public function setRequestFilter(RequestStack $requestStack): void
    {
        $this->requestFilter = $requestStack;
    }


    /**
     * @internal
     */
    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @internal
     */
    #[Required]
    public function setFormBuilder(FormFactoryInterface $formBuilder): void
    {
        $this->formBuilder = $formBuilder;
    }

    #[PostMount]
    public function initValues(): void
    {
        foreach ($this->getFilters() as $filter) {
            if (!isset($this->valuesFilters[$filter->getName()])) {
                $this->valuesFilters[$filter->getName()] = $filter->getDefaultValue();
            }
        }
    }

    public function setRepository(string $repository): void
    {
        $this->repository = $repository;
    }

    #[LiveAction]
    public function updateFilters(): void
    {
        $this->emit('filtersUpdated');
        $this->emit('updatedValues', $this->valuesFilters);
    }

    public function getFormFilters(): FormView
    {
        $form = $this->formBuilder->create(FormType::class, null, [
            'translation_domain' => $this->getDefaultTransDomain(),
        ]);
        foreach ($this->getFilters() as $filter) {
            $options = array_merge_recursive($filter->getDefaultOptions(), $filter->getOptions());
            $form->add($filter->getName(), $filter->getType(), $options);
        }

        return $form->createView();
    }

    abstract protected function setFilters(): iterable;

    private function getFilters(): array
    {
        if (!$this->filters) {
            $this->filters = iterator_to_array($this->setFilters());
        }
        return $this->filters;
    }

    public function getValuesFilters(): array
    {
        return $this->valuesFilters;
    }

    public function setValuesFilters(array $valuesFilters): void
    {
        $this->valuesFilters = $valuesFilters;
    }

    /**
     * @throws Exception
     */
    protected function getDefaultQuery(): QueryBuilder
    {
        if ($this->repository === null)
            throw new Exception('You must set the repository before using the default query');

        return $this->entityManager->getRepository($this->repository)
            ->createQueryBuilder('entity');
    }


    /**
     * @throws Exception
     */
    public function addSearchQuery(QueryBuilder $builder): QueryBuilder
    {
        foreach ($this->getFilters() as $filter) {
            $queryParam = [];
            $value = $this->valuesFilters[$filter->getName()];
            if ($this->valuesFilters[$filter->getName()] === null || $this->valuesFilters[$filter->getName()] === '' || empty($this->valuesFilters[$filter->getName()])) {
                $value = $filter->getDefaultValue();
            }

            if ($value !== '' && !empty($value)) {
                if (method_exists($filter, 'getMultipleWord') && $filter->getMultipleWord() !== null) {
                    $value = explode(' ', $value);
                }
                if(!is_array($value)) {
                    $value = [$value];
                }
                foreach ($value as $key => $v) {
                    foreach ($filter->getParams() as $param) {
                        if ($param instanceof \Closure) {
                            $queryParam[$key][] = $param($builder, $v);
                        } else {
                            $filterName = $filter->getName() . '_' . $key;
                            if ($filter->getSearchType() === 'like') {
                                $v = '%' . $v . '%';
                            }
                            $queryParam[$key][] = $builder->expr()->{$filter->getSearchType()}($param, ':' . $filterName);
                            $builder->setParameter($filterName, $v);
                        }
                    }
                }
                if (method_exists($filter, 'getMultipleWord') && $filter->getMultipleWord() === 'and') {
                    foreach ($queryParam as $param) {
                        $builder->andWhere(
                            $builder->expr()->orX(...$param)
                        );
                    }
                } else {
                    foreach ($queryParam as $param) {
                        $builder->orWhere(
                            $builder->expr()->orX(...$param)
                        );
                    }
                }
            }
        }

        return $builder;
    }

    /**
     * @throws Exception
     * @deprecated
     */
    public function getQuery(): QueryBuilder
    {
        $builder = $this->getDefaultQuery();
        return $this->addSearchQuery($builder);
    }

    public function getDefaultData(): mixed
    {
        return null;
    }

    public function getData(): mixed
    {
        $datasTofilter = $this->getDefaultData();
        if ($datasTofilter === null) {
            $datasTofilter = $this->getDefaultQuery();
        }
        if ($datasTofilter instanceof QueryBuilder) {
            return $this->addSearchQuery($datasTofilter);
        }
        $filters = $this->getFilters();
        if (is_array($datasTofilter)) {
            foreach ($filters as $filter) {
                $value = $this->valuesFilters[$filter->getName()];
                if ($value !== null && $value !== '' && !empty($value)) {
                    $datasTofilter = array_filter($datasTofilter, function ($item) use ($filter, $value) {
                        foreach ($filter->getParams() as $param) {
                            return $this->{$filter->getSearchType()}($item, $param, $value);
                        }
                    });
                }
                return $datasTofilter;
            }
        }
        return [];
    }

    private function getValue($item, $param): ?string
    {
        switch ($item) {
            case is_array($item):
                return $item[$param];
            case is_object($item):
                $getter = 'get' . ucfirst($param);
                return $item->$getter();
            default:
                return null;
        }
    }

    private function eq($item, $param, $value): bool
    {
        return $this->getValue($item, $param) === $value;
    }

    private function like($item, $param, $value): bool
    {
        return str_contains($this->getValue($item, $param), $value);
    }

    protected function getDefaultTransDomain(): string
    {
        return $this->defaultTransDomain;
    }
}
