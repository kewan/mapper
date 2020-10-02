<?php

namespace Tests\Kewan\Mapper;

use Kewan\Mapper\PathFinder;
use PHPUnit\Framework\TestCase;

class PathFinderTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_find_all_available_paths_from_an_array()
    {
        $subject = [
            'product' => [
                'name'  => 'Iphone 11',
                'image' => 'http://example.com/image.jpg',
            ],
            'user'    => [
                'name' => 'Jonn',
                'avatar' => [
                    'image' => 'http://example.com/avatar.jpg'
                ]
            ],
            'Image' => [
                'Url' => 'http://someimage.jpg'
            ],
        ];


        $reader = new PathFinder($subject);

        $paths = $reader->paths();

        $this->assertContains('product.name', $paths);
        $this->assertContains('product.image', $paths);
        $this->assertContains('user.name', $paths);
        $this->assertContains('user.avatar.image', $paths);

    }

    /**
     * @test
     */
    public function it_can_find_all_available_paths_from_an_object()
    {
        $subject = new \stdClass();
        $subject->name = "iphone 11";
        $subject->image = "http://example.jpg";
        $subject->tags = ['one', 'two'];
        $subject->user = new \stdClass();
        $subject->user->name = 'JOhn';
        $subject->user->avatar = new \stdClass();
        $subject->user->avatar->image = 'http://example.png';

        $reader = new PathFinder($subject);

        $paths = $reader->paths();

        $this->assertContains('name', $paths);
        $this->assertContains('image', $paths);
        $this->assertContains('tags', $paths);
        $this->assertContains('user.name', $paths);
        $this->assertContains('user.avatar.image', $paths);
    }

}
