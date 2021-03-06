<?php

namespace Pinq\Tests\Integration\Traversable;

class IndexByTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->indexBy(function () {

        });
    }

    /**
     * @dataProvider everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'indexBy']);
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatIndexByElementIndexesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $indexedElements = $traversable->indexBy(function ($i) { return $i; });

        $this->assertMatches($indexedElements, array_combine($data, $data));
    }

    /**
     * @dataProvider everything
     */
    public function testThatIndexByNullReturnsLastArrayWithLastElement(\Pinq\ITraversable $traversable, array $data)
    {
        $indexedElements = $traversable->indexBy(function () { return null; });

        $this->assertMatches(
                $indexedElements,
                empty($data) ? [] : [null => end($data)]);
    }
}
