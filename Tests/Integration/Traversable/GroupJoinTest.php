<?php

namespace Pinq\Tests\Integration\Traversable;

class GroupJoinTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->groupJoin([])->on(function ($i) { })->to(function ($k) { });
    }

    /**
     * @dataProvider everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->groupJoin([])->on($function)->to($function);
        });

        $this->assertThatExecutionIsDeferred(function (callable $function) use ($traversable) {
            return $traversable->groupJoin([])->onEquality($function, $function)->to($function);
        });
    }

    /**
     * @dataProvider everything
     */
    public function testGroupJoinOnTrueProducesTheCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin($data)->on(function () { return true; })
                ->to(function ($outerValue, \Pinq\ITraversable $group) {
                    return [$outerValue, $group->asArray()];
                });

        $correctResult = [];

        foreach ($data as $outerValue) {
            $correctResult[] = [$outerValue, $data];
        }

        $this->assertMatchesValues($traversable, $correctResult);
    }

    /**
     * @dataProvider everything
     */
    public function testGroupJoinOnFalseProducesEmptyLeftJoin(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable =
                $traversable->groupJoin($data)->on(function () { return false; })
                ->to(function ($outerValue, \Pinq\ITraversable $group) {
                    return [$outerValue, $group->asArray()];
                });

        $emptyLeftJoin = [];

        foreach ($data as $value) {
            $emptyLeftJoin[] = [$value, []];
        }

        $this->assertMatchesValues($traversable, $emptyLeftJoin);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->groupJoin([1,2,2,3,'4','5'])
                ->on(function ($outer, $inner) { return $outer === $inner; })
                ->to(function ($outer, \Pinq\ITraversable $values) {
                    return $outer . '-' . $values->implode('-');
                });

        $this->assertMatchesValues($traversable, ['1-1', '2-2-2', '3-3', '4-', '5-', '6-', '7-', '8-', '9-', '10-']);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinOnEqualityProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable =
                $traversable->groupJoin([1, 2, 2, 3, '4', '5'])
                ->onEquality(function ($outer) { return $outer; }, function ($inner) { return $inner; })
                ->to(function ($outer, \Pinq\ITraversable $values) { return $outer . '-' . $values->implode('-'); });

        $this->assertMatchesValues($traversable, ['1-1', '2-2-2', '3-3', '4-', '5-', '6-', '7-', '8-', '9-', '10-']);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinWithStringsProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable =
                $traversable->groupJoin(['foo', 'bar', 'baz', 'tear', 'cow', 'tripod', 'whisky', 'sand', 'which'])
                ->onEquality(function ($outer) { return $outer;}, 'strlen')
                ->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                    return $outer . ':' . $innerGroup->implode('|');
                });

        $this->assertMatchesValues(
                $traversable,
                [
                    '1:',
                    '2:',
                    '3:foo|bar|baz|cow',
                    '4:tear|sand',
                    '5:which',
                    '6:tripod|whisky',
                    '7:',
                    '8:',
                    '9:',
                    '10:'
                ]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testGroupJoinWithGreaterThanProducesCorrectResult(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable =
                $traversable->groupJoin($traversable)->on(function ($outer, $inner) {
                    return $outer >= $inner;
                })->to(function ($outer, \Pinq\ITraversable $innerGroup) {
                    return $outer . ':' . $innerGroup->implode('|');
                });

        $this->assertMatchesValues(
                $traversable,
                [
                    '1:1',
                    '2:1|2',
                    '3:1|2|3',
                    '4:1|2|3|4',
                    '5:1|2|3|4|5',
                    '6:1|2|3|4|5|6',
                    '7:1|2|3|4|5|6|7',
                    '8:1|2|3|4|5|6|7|8',
                    '9:1|2|3|4|5|6|7|8|9',
                    '10:1|2|3|4|5|6|7|8|9|10'
                ]);
    }
}
