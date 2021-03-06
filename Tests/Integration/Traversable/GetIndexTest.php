<?php

namespace Pinq\Tests\Integration\Traversable;

class GetIndexTest extends TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatIndexReturnsCorrectValue(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $traversable[$key]);
        }
    }
}
