<?php

namespace Pinq;

/**
 * The API for subsequent orderings of a traversable query
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IOrderedTraversable extends ITraversable
{
    /**
     * Subsequently orders the results using the supplied function according to
     * the supplied direction
     *
     * @param  callable          $function
     * @param  int               $direction
     * @return IOrderedTraversable
     */
    public function thenBy(callable $function, $direction);

    /**
     * Subsequently orders the results using the supplied function ascendingly
     *
     * @param  callable          $function
     * @return IOrderedTraversable
     */
    public function thenByAscending(callable $function);

    /**
     * Subsequently orders the results using the supplied function descendingly
     *
     * @param  callable          $function
     * @return IOrderedTraversable
     */
    public function thenByDescending(callable $function);
}
