<?php

namespace Tests\Kewan\Mapper;

use function foo\func;
use Kewan\Mapper\Reader;
use PHPUnit\Framework\TestCase;

require_once 'DemoProduct.php';

class ReaderTest extends TestCase
{

    /** @test */
    public function it_can_read_from_parameter()
    {
        $subject       = new \stdClass();
        $subject->name = "John";

        $reader = new Reader($subject);

        $this->assertEquals('John', $reader->get('name'));
    }

    /** @test */
    public function it_provides_default_if_not_found()
    {
        $subject       = new \stdClass();
        $subject->name = "John";

        $reader = new Reader($subject);

        $this->assertEquals('unknown', $reader->get('email', 'unknown'));
    }

    /** @test */
    public function it_can_read_from_array()
    {
        $subject = [
            'name' => 'John',
        ];

        $reader = new Reader($subject);

        $this->assertEquals('John', $reader->get('name'));
        $this->assertEquals('Mr', $reader->get('title', 'Mr'));
    }

    /** @test */
    public function it_can_read_from_nested_objects()
    {
        $contact       = new \stdClass();
        $contact->name = 'John';

        $contact->company       = new \stdClass();
        $contact->company->logo = ['url' => 'http://example.com/img.jpg'];

        $reader = new Reader($contact);

        $this->assertEquals('John', $reader->get('name'));
        $this->assertEquals('http://example.com/img.jpg', $reader->get('company.logo.url', 'baseimg.jpg'));
    }

    /** @test */
    public function it_can_read_from_multi_dimensional_array()
    {
        $subject = [
            'contact' => [
                'name' => 'John',
            ],
            'company' => [
                'logo' => [
                    'url' => 'http://example.com/img.jpg',
                ],
            ],
        ];

        $reader = new Reader($subject);

        $this->assertEquals('John', $reader->get('contact.name'));
        $this->assertEquals('http://example.com/img.jpg', $reader->get('company.logo.url', 'baseimg.jpg'));
    }

    /** @test */
    public function it_can_read_from_methods()
    {
        $subject = new DemoProduct();
        $reader  = new Reader($subject);

        $this->assertEquals($subject->sku, $reader->get('sku'));
        $this->assertEquals($subject->getTitle(), $reader->get('getTitle'));
    }

    /** @test */
    public function it_can_read_using_callback()
    {
        $contact       = new \stdClass();
        $contact->name = 'John';

        $contact->company       = new \stdClass();
        $contact->company->name = 'Acme';

        $reader = new Reader($contact);

        $this->assertEquals('John From Acme', $reader->get(function($contact) {
            return $contact->name . ' From ' . $contact->company->name;
        }));
    }

}
