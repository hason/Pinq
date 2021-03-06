<?php

namespace Pinq;

use Pinq\Queries;
use Pinq\Queries\Requests;
use Pinq\Queries\Segments;

/**
 * The standard queryable class, fully implements the queryable API
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Queryable implements IQueryable, IOrderedTraversable, IGroupedTraversable
{
    /**
     * The query provider implementation for this queryable
     *
     * @var Providers\IQueryProvider
     */
    protected $provider;

    /**
     * The function converter from the query provider
     *
     * @var Parsing\IFunctionToExpressionTreeConverter
     */
    protected $functiontConverter;

    /**
     * The query scope of this instance
     *
     * @var Queries\IScope
     */
    protected $scope;

    /**
     * The underlying values iterator if loaded
     *
     * @var \Iterator|null
     */
    protected $valuesIterator = null;

    public function __construct(Providers\IQueryProvider $provider, Queries\IScope $scope = null)
    {
        $this->provider = $provider;
        $this->functiontConverter = $provider->getFunctionToExpressionTreeConverter();
        $this->scope = $scope ?: new Queries\Scope([]);
    }

    /**
     * Returns a new queryable instance with the supplied query segment
     * appended to the current scope
     *
     * @param Queries\ISegment $segment The new segment
     * @return IQueryable
     */
    final protected function newSegment(Queries\ISegment $segment)
    {
        return $this->provider->createQueryable($this->scope->append($segment));
    }

    /**
     * Returns a new queryable instance with the supplied query segment
     * updating the last segment of the current scope
     *
     * @param Queries\ISegment $segment The new segment
     * @return IQueryable
     */
    final protected function updateLastSegment(Queries\ISegment $segment)
    {
        return $this->provider->createQueryable($this->scope->updateLast($segment));
    }

    /**
     * Returns the requested query from the query provider.
     *
     * @param Queries\IRequest $request The request to load
     * @return mixed The result of the request query
     */
    private function loadQuery(Queries\IRequest $request)
    {
        return $this->provider->load(new Queries\RequestQuery($this->scope, $request));
    }

    /**
     * Loads the values iterator if not already load
     *
     * @return void
     */
    private function load()
    {
        if ($this->valuesIterator === null) {
            $this->valuesIterator = $this->loadQuery(new Requests\Values());
        }
    }

    final public function asArray()
    {
        $this->load();
        $values = Utilities::toArray($this->valuesIterator);

        if (!$this->valuesIterator instanceof \ArrayIterator) {
            $this->valuesIterator = new \ArrayIterator($values);
        }

        return $values;
    }

    public function asTraversable()
    {
        $this->load();

        return new Traversable($this->valuesIterator);
    }

    public function asCollection()
    {
        return new Collection($this->getIterator());
    }

    public function asQueryable()
    {
        return $this;
    }

    public function asRepository()
    {
        if ($this->provider instanceof Providers\IRepositoryProvider) {
            return $this->provider->createRepository($this->scope);
        } else {
            return (new Collection($this->getIterator()))->asRepository();
        }
    }

    final public function getIterator()
    {
        $this->load();

        return $this->valuesIterator;
    }

    final public function getProvider()
    {
        return $this->provider;
    }

    public function getScope()
    {
        return $this->scope;
    }

    final protected function convert(callable $function = null)
    {
        return $function === null ? null : $this->functiontConverter->convert($function);
    }

    // <editor-fold defaultstate="collapsed" desc="Query segments">

    public function select(callable $function)
    {
        return $this->newSegment(new Segments\Select($this->convert($function)));
    }

    public function selectMany(callable $function)
    {
        return $this->newSegment(new Segments\SelectMany($this->convert($function)));
    }

    public function indexBy(callable $function)
    {
        return $this->newSegment(new Segments\IndexBy($this->convert($function)));
    }

    public function where(callable $predicate)
    {
        return $this->newSegment(new Segments\Filter($this->convert($predicate)));
    }

    public function groupBy(callable $function)
    {
        return $this->newSegment(new Segments\GroupBy([$this->convert($function)]));
    }

    public function andBy(callable $function)
    {
        $segments = $this->scope->getSegments();
        $lastSegment = end($segments);

        if (!$lastSegment instanceof Segments\GroupBy) {
            throw new PinqException(
                    'Invalid call to %s: %s::%s must be called first',
                    __METHOD__,
                    __CLASS__,
                    'GroupBy');
        }

        return $this->updateLastSegment($lastSegment->andBy($this->convert($function)));
    }

    public function join($values)
    {
        return new JoiningOnQueryable($this->provider, $this->scope, $values, false);
    }

    public function groupJoin($values)
    {
        return new JoiningOnQueryable($this->provider, $this->scope, $values, true);
    }

    public function union($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::UNION, $values));
    }

    public function intersect($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::INTERSECT, $values));
    }

    public function difference($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::DIFFERENCE, $values));
    }

    public function append($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::APPEND, $values));
    }

    public function whereIn($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::WHERE_IN, $values));
    }

    public function except($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::EXCEPT, $values));
    }

    public function skip($amount)
    {
        return $this->newSegment(new Segments\Range($amount, null));
    }

    public function take($amount)
    {
        return $this->newSegment(new Segments\Range(0, $amount));
    }

    public function slice($start, $amount)
    {
        return $this->newSegment(new Segments\Range($start, $amount));
    }

    public function orderByAscending(callable $function)
    {
        return $this->newSegment(new Segments\OrderBy([$function], [true]));
    }

    public function orderByDescending(callable $function)
    {
        return $this->newSegment(new Segments\OrderBy([$function], [false]));
    }

    private function validateOrderBy()
    {
        $segments = $this->scope->getSegments();
        $lastSegment = end($segments);

        if (!$lastSegment instanceof Segments\OrderBy) {
            throw new PinqException(
                    'Invalid call to %s: %s::%s must be called first',
                    __METHOD__,
                    __CLASS__,
                    'OrderBy');
        }

        return $lastSegment;
    }

    public function thenBy(callable $function, $direction)
    {
        return $this->updateLastSegment($this->validateOrderBy()->thenBy(
                $this->convert($function),
                $direction !== Direction::DESCENDING));
    }

    public function thenByAscending(callable $function)
    {
        return $this->updateLastSegment($this->validateOrderBy()->thenBy($this->convert($function), true));
    }

    public function thenByDescending(callable $function)
    {
        return $this->updateLastSegment($this->validateOrderBy()->thenBy($this->convert($function), false));
    }

    public function orderBy(callable $function, $direction)
    {
        return $this->newSegment(new Segments\OrderBy(
                [$function],
                [$direction !== Direction::DESCENDING]));
    }

    public function unique()
    {
        return $this->newSegment(new Segments\Unique());
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Query Requests">

    public function offsetExists($index)
    {
        return $this->loadQuery(new Requests\IssetIndex($index));
    }

    public function offsetGet($index)
    {
        return $this->loadQuery(new Requests\GetIndex($index));
    }

    public function offsetSet($index, $value)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function offsetUnset($index)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function first()
    {
        return $this->loadQuery(new Requests\First());
    }

    public function last()
    {
        return $this->loadQuery(new Requests\Last());
    }

    public function count()
    {
        return $this->loadQuery(new Requests\Count());
    }

    public function exists()
    {
        return $this->loadQuery(new Requests\Exists());
    }

    public function contains($value)
    {
        return $this->loadQuery(new Requests\Contains($value));
    }

    public function aggregate(callable $function)
    {
        return $this->loadQuery(new Requests\Aggregate($this->convert($function)));
    }

    public function all(callable $function = null)
    {
        return $this->loadQuery(new Requests\All($this->convert($function)));
    }

    public function any(callable $function = null)
    {
        return $this->loadQuery(new Requests\Any($this->convert($function)));
    }

    public function maximum(callable $function = null)
    {
        return $this->loadQuery(new Requests\Maximum($this->convert($function)));
    }

    public function minimum(callable $function = null)
    {
        return $this->loadQuery(new Requests\Minimum($this->convert($function)));
    }

    public function sum(callable $function = null)
    {
        return $this->loadQuery(new Requests\Sum($this->convert($function)));
    }

    public function average(callable $function = null)
    {
        return $this->loadQuery(new Requests\Average($this->convert($function)));
    }

    public function implode($delimiter, callable $function = null)
    {
        return $this->loadQuery(new Requests\Implode($delimiter, $this->convert($function)));
    }

    // </editor-fold>
}
