<?php

namespace Pinq\Tests\Integration\Collection;

class ClearTest extends CollectionTest
{
    /**
     * @dataProvider everything
     */
    public function testThatClearRemovesAllItems(\Pinq\ICollection $collection, array $data)
    {
        $collection->clear();

        $this->assertMatchesValues($collection, []);
    }
}
