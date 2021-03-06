<?php

use PHPUnit\Framework\TestCase;

class MainViewTest extends TestCase
{
    private $sut;
    private $recipeViewMock;

    function setUp(): void
    {
        $this->sut = new MainView();

        $this->recipeViewMock = $this->getMockBuilder(AddRecipeView::class)
            ->disableOriginalConstructor()
            ->setMethods(['generateOutput'])
            ->getMock();
    }

    /** @test */
    function shouldGenerateMainTitle()
    {
        $actual = $this->sut->generateMainTitle();
        $expected = '<h1>My Cook Book</h1>';

        $this->assertEquals($actual, $expected);
    }

    /** @test */
    function shouldCallOnGenerateMainTitle()
    {
        $mainViewMock = $this->getMockBuilder(MainView::class)
            ->setMethods(['generateMainTitle'])
            ->getMock();

        $mainViewMock->expects($this->once())->method('generateMainTitle');

        $mainViewMock->render($this->recipeViewMock);
    }

    /** @test */
    function shouldCallOnAddRecipeMethodGenerateOutput()
    {
        $this->recipeViewMock->expects($this->once())->method('generateOutput');

        $this->sut->render($this->recipeViewMock);
    }

    /** @test */
    function shouldCallOnRenderRecipesFromRender()
    {
        $mainViewMock = $this->getMockBuilder(MainView::class)
            ->setMethods(['renderRecipes'])
            ->getMock();

        $mainViewMock->expects($this->once())->method('renderRecipes');

        $mainViewMock->render($this->recipeViewMock);
    }

    /** @test */
    function shouldReturnNoRecipesFromRenderRecipesIfCollectionNull()
    {
        $actual = $this->sut->renderRecipes(null);

        $expected = '<p>No recipes</p>';

        $this->assertEquals($actual, $expected);
    }

    /** @test */
    function shouldDisplayRecipe()
    {
        $recipeMock = $this->createMock(Recipe::class);
        $ingredientsMock = $this->createMock(Ingredient::class);
        $instructionsMock = $this->createMock(Instruction::class);
        $instructionCollectionMock = $this->createMock(InstructionsCollection::class);
        $recipeCollectionMock = $this->createMock(RecipeCollection::class);

        $ingredientsMock->method('getName')->willReturn('Potatoes');
        $ingredientsMock->method('getAmount')->willReturn(2.0);
        $ingredientsMock->method('getMeasurement')->willReturn('pcs');

        $instructionsMock->method('getInstruction')->willReturn('This is the first instruction');
        $instructionCollectionMock->method('getInstructions')->willReturn([$instructionsMock]);


        $recipeMock->method('getTitle')->willReturn('Meatloaf');
        $recipeMock->method('getAuthor')->willReturn('Per Morberg');
        $recipeMock->method('getServings')->willReturn(2);
        $recipeMock->method('getTagName')->willReturn('Dinner');
        $recipeMock->method('getIngredients')->willReturn([$ingredientsMock]);
        $recipeMock->method('getInstructions')->willReturn($instructionCollectionMock);

        $recipeCollectionMock->method('getRecipes')->willReturn([$recipeMock]);

        $actual = $this->sut->render($this->recipeViewMock, $recipeCollectionMock);

        $expectedTitle = 'Meatloaf';
        $expectedAuthor = 'Per Morberg';
        $expectedServings = '2';
        $expectedTag = 'Dinner';

        $this->assertStringContainsString($expectedTitle, $actual);
        $this->assertStringContainsString($expectedAuthor, $actual);
        $this->assertStringContainsString($expectedServings, $actual);
        $this->assertStringContainsString($expectedTag, $actual);
    }

    /** @test */
    function shouldRender2Ingredients()
    {
        $recipeMock = $this->createMock(Recipe::class);
        $potatoMock = $this->createMock(Ingredient::class);
        $milkMock = $this->createMock(Ingredient::class);

        $potatoMock->method('getName')->willReturn('Potatoes');
        $potatoMock->method('getAmount')->willReturn(2.0);
        $potatoMock->method('getMeasurement')->willReturn('pcs');

        $milkMock->method('getName')->willReturn('Milk');
        $milkMock->method('getAmount')->willReturn(4.0);
        $milkMock->method('getMeasurement')->willReturn('dl');

        $recipeMock->method('getIngredients')->willReturn([$potatoMock, $milkMock]);

        $actual = $this->sut->renderIngredients($recipeMock->getIngredients());

        $expectedPotatoes = 'Potatoes';
        $expectedMilk = 'Milk';

        $this->assertStringContainsString($expectedPotatoes, $actual);
        $this->assertStringContainsString($expectedMilk, $actual);
    }

    /** @test */
    function shouldRender2Instructions()
    {
        $instructionOneMock = $this->createMock(Instruction::class);
        $instructionTwoMock = $this->createMock(Instruction::class);
        $collectionMock = $this->createMock(InstructionsCollection::class);

        $instructionOneMock->method('getInstruction')->willReturn('Set oven to 200 deg.');
        $instructionTwoMock->method('getInstruction')->willReturn('Add milk and flour to the bucket.');

        $collectionMock->method('getInstructions')->willReturn([$instructionOneMock, $instructionTwoMock]);

        $actual = $this->sut->renderInstructions($collectionMock);

        $expectOne = 'Set oven to 200 deg.';
        $expectTwo = 'Add milk and flour to the bucket.';

        $this->assertStringContainsString($expectOne, $actual);
        $this->assertStringContainsString($expectTwo, $actual);
    }
}
