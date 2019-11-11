<?php

use PHPUnit\Framework\TestCase;
use TheSeer\Tokenizer\Exception;

class IngredientTest extends TestCase
{
    /** @test */
    function shouldAcceptIngredient()
    {
        $input = 'Flour';
        $sut = new Ingredient($input);
        $actual = $sut->getIngredient();

        $expected = 'Flour';

        $this->assertEquals($actual, $expected);
    }

    /** @test */
    function shouldThrowExceptionOnIngredientLongerThan60Characters()
    {
        $this->expectException(TooLongIngredientException::class);

        $input = 'This is a veeeeery long ingredient that is not acceptable to our system';
        new Ingredient($input);
    }

    /** @test */
    function shouldThrowExceptionOnIngredientShorterThan2Characters()
    {
        $this->expectException(TooShortIngredientException::class);

        $input = 'Po';
        new Ingredient($input);
    }
}