<?php


namespace App\Http\QueryFilters;


use Closure;
use Illuminate\Support\Str;

abstract class Filter
{
    /**
     * Handle query filter and apply it.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!request()->has($this->filterName())) {
            return $next($request);
        }

        $builder = $next($request);

        return $this->applyFilter($builder);
    }

    protected abstract function applyFilter($builder);

    /**
     * Get filter name in snake case.
     *
     * @return string
     */
    protected function filterName()
    {
        return Str::snake(class_basename($this));
    }
}
