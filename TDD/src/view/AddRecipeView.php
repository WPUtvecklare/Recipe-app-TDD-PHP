<?php

class AddRecipeView
{
    protected static $addRecipe = __CLASS__  . '::addRecipe';

    private static $title = "title";
    private static $author = "author";
    private static $servings = "servings";
    private static $tag = "tag";
    private static $submitRecipe = "submitRecipe";

    private $factory;

    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    public function generateOutput()
    {
        $response = "";
        if ($this->userWantsToAddRecipe()) {
            $response .= $this->renderAddRecipe();
        } else {
            $response .= $this->generateAddRecipeBtnForm();
        }

        return $response;
    }

    public function userWantsToAddRecipe(): bool
    {
        return isset($_POST[self::$addRecipe]);
    }

    public function generateAddRecipeBtnForm(): string
    {
        return '
        <form method="POST" class="add-recipe-btn-form">
            <input class="add-recipe-btn" type="submit" name="' . self::$addRecipe . '" value="Add Recipe!" />
        </form>
        ';
    }

    public function renderAddRecipe(): string
    {
        return file_get_contents(__DIR__ . "/partials/addRecipeForm.php");
    }

    public function createRecipe(): Recipe
    {
        $title = $this->getTitle();
        $recipe = $this->factory->instanciateRecipe($title);

        return $recipe;
    }

    public function getRecipe(): Recipe
    {
        $recipe = $this->createRecipe();
        $author = $this->getAuthor();
        $servings = $this->getServings();
        $tag = $this->getTag();
        $recipe->setAuthor($author);
        $recipe->setServings($servings);
        $recipe->setTagName($tag);

        $ingredients = $this->getIngredients();

        foreach ($ingredients as $ingredient) {
            $recipe->addIngredient($ingredient);
        }

        $instructions = $this->getInstructionsToRecipe();
        $recipe->addInstructions($instructions);

        return $recipe;
    }

    private function getTitle(): string
    {
        if (isset($_GET[self::$title]) && !empty($_GET[self::$title])) {
            return $_GET[self::$title];
        } else {
            throw new RecipeTitleMissingException();
        }
    }

    public function getAuthor(): string
    {
        if (isset($_GET[self::$author]) && !empty($_GET[self::$author])) {
            return $_GET[self::$author];
        } else {
            throw new AuthorMissingException();
        }
    }

    public function getServings(): int
    {
        if (isset($_GET[self::$servings]) && !empty($_GET[self::$servings])) {
            return $_GET[self::$servings];
        } else {
            throw new ServingsMissingException();
        }
    }

    public function getTag(): string
    {
        if (isset($_GET[self::$tag]) && !empty($_GET[self::$tag])) {
            return $_GET[self::$tag];
        } else {
            throw new TagMissingException();
        }
    }

    public function getIngredients()
    {
        $count = $this->getIngredientCount();
        $this->validateOnEmptyInputs($count);

        $ingredients = array();

        for ($i = 0; $i < $count; $i++) {
            $ind = $i + 1;
            $ingredientName = $_GET["ingredient-name" . $ind];
            $ingredientAmount = (float) $_GET["ingredient-amount" . $ind];
            $ingredientMeasure = $_GET["ingredient-measurement" . $ind];

            $amount = $this->factory->instanciateAmount($ingredientAmount);
            $measure = $this->factory->instanciateMeasurement($ingredientMeasure);

            $ingredient = $this->factory->instanciateIngredient($ingredientName, $amount, $measure);

            $ingredients[] = $ingredient;
        }

        return $ingredients;
    }

    private function getIngredientCount()
    {
        $count = 0;

        foreach ($_GET as $k => $v) {
            $names = substr($k, 0, 15);
            if ($names == 'ingredient-name') {
                $count++;
            }
        }
        return $count;
    }

    public function validateOnEmptyInputs(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $index = $i + 1;
            if (!isset($_GET["ingredient-name" . $index]) && empty($_GET["ingredient-name" . $index])) {
                throw new IngredientNameMissingException();
            }

            if (!isset($_GET["ingredient-amount" . $index]) && empty($_GET["ingredient-amount" . $index])) {
                throw new IngredientAmountMissingException();
            }

            if (!isset($_GET["ingredient-measurement" . $index]) && empty($_GET["ingredient-measurement" . $index])) {
                throw new IngredientMeasurementMissingException();
            }
        }
    }

    public function getInstructionsToRecipe(): InstructionsCollection
    {
        $collection = $this->factory->instanciateInstructionsCollection();
        $instructions = $this->getInstructions();

        foreach ($instructions as $instruction) {
            $collection->addInstruction($instruction);
        }

        return $collection;
    }

    public function getInstructions()
    {
        $count = $this->getInstructionRequestsCount();
        $instructions = array();

        for ($i = 1; $i <= $count; $i++) {
            $instruction = $_GET["instruction" . $i];
            $instructions[] = $this->addInstruction($instruction, $i);
        }

        return $instructions;
    }

    private function getInstructionRequestsCount()
    {
        $count = 0;

        foreach ($_GET as $k => $v) {
            $names = substr($k, 0, 11);
            if ($names == 'instruction') {
                $count++;
            }
        }
        return $count;
    }

    public function addInstruction($instruction, $index): Instruction
    {
        if (isset($_GET["instruction" . $index]) && !empty($_GET["instruction" . $index])) {
            return $this->factory->instanciateInstruction($instruction);
        } else {
            throw new InstructionMissingException();
        }
    }

    public function userWantsToSubmitRecipe(): bool
    {
        return isset($_GET[self::$submitRecipe]);
    }
}
