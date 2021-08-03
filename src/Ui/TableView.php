<?php

namespace Laravolt\Ui;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Laravolt\Suitable\Concerns\SourceResolver;
use Livewire\Component;
use Livewire\WithPagination;

abstract class TableView extends Component
{
    use SourceResolver;
    use WithPagination;

    private const DEFAULT_PER_PAGE = 15;

    public bool $showSearchbox = true;

    protected bool $showPerPage = true;

    protected string $searchName = 'search';

    protected string $title = '';

    protected mixed $data;

    protected $paginationView = 'laravolt::pagination.livewire.simple';

    protected $queryString = [
        'page' => ['except' => 1],
        'search' => ['except' => ''],
        'sort' => ['except' => null],
        'direction',
        'perPage' => ['except' => self::DEFAULT_PER_PAGE],
    ];

    public string $search = '';

    public int $perPage = self::DEFAULT_PER_PAGE;

    public ?string $sort = null;

    public ?string $direction = null;

    public array $filters = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filters = [];
    }

    public function changePerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    public function sortBy($column)
    {
        if ($this->sort !== $column) {
            $this->direction = null;
        }

        $this->sort = $column;
        $this->direction = match ($this->direction) {
            null, 'desc' => 'asc',
            'asc' => 'desc',
        };
    }

    public function summary(): string
    {
        if (!$this->data instanceof LengthAwarePaginator) {
            return '';
        }

        $from = (($this->data->currentPage() - 1) * $this->data->perPage()) + 1;
        $total = $this->data->total();

        if ($this->data->hasMorePages()) {
            $to = $from + $this->data->perPage() - 1;
        } else {
            $to = $total;
        }

        if ($total > 0) {
            return trans('suitable::pagination.summary', compact('from', 'to', 'total'));
        }

        return trans('suitable::pagination.empty');
    }

    public function render()
    {
        $this->data = $this->data();
        $filterClasses = collect($this->filters())->keyBy(fn ($item) => $item->key());
        foreach ($this->filters as $key => $value) {
            if ($filterClasses->has($key)) {
                $this->data = $filterClasses->get($key)->apply($this->data, $value);
            }
        }

        $this->data = $this->resolve($this->data);

        $perPageOptions = [];
        if ($this->data instanceof LengthAwarePaginator) {
            $this->paginationView = 'laravolt::pagination.livewire.default';
            if ($this->showPerPage) {
                $perPageOptions = array_unique(array_merge([5, 15, 30, 50, 100, 250], [$this->data->perPage()]));
                sort($perPageOptions);
            }
        }

        return view(
            'laravolt::ui-component.table-view.container',
            [
                'data' => $this->data,
                'columns' => $this->columns(),
                'filters' => $this->filters,
                'sort' => $this->sort,
                'direction' => $this->direction,
                'showPerPage' => $this->showPerPage,
                'perPageOptions' => $perPageOptions,
                'paginationView' => $this->paginationView,
            ]
        );
    }

    abstract public function data();

    abstract public function columns(): array;

    public function filters(): array
    {
        return [];
    }

    protected function sortPayload()
    {
        return [
            'sort' => $this->sort,
            'direction' => $this->direction,
        ];
    }
}
