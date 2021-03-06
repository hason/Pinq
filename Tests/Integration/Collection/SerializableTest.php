<?php

namespace Pinq\Tests\Integration\Collection;

class SerializableTest extends CollectionTest
{
    /**
     * @dataProvider everything
     */
    public function testThatCollectionIsSerializable(\Pinq\ICollection $collection, array $data)
    {
        $serializedCollection = serialize($collection);
        $unserializedCollection = unserialize($serializedCollection);

        $this->assertEquals(
                $collection->asArray(),
                $unserializedCollection->asArray());
    }

    /**
     * @dataProvider everything
     */
    public function testThatCollectionIsSerializableAfterQueries(\Pinq\ICollection $collection, array $data)
    {
        $collection = $collection
                ->where(function ($i) { return $i !== false; });
        $serializedCollection = serialize($collection);
        $unserializedCollection = unserialize($serializedCollection);

        $this->assertEquals(
                $collection->asArray(),
                $unserializedCollection->asArray());
    }
}
