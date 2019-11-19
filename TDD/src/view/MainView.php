<?php

class MainView
{

    public function generateMainTitle()
    {
        return '<h1>My Cook Book</h1>';
    }

    public function render($view, $collection = null)
    {
        return '<!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <title>Cook Book</title>
          </head>
          <body>
            ' . $this->generateMainTitle() . '
            <div class="container">
            ' . $view->generateOutput() . '
            ' . $this->renderRecipes($collection) . '
            </div>
           </body>
        </html>
      ';
    }

    public function renderRecipes($collection)
    {
        if ($collection == null || empty($collection->getRecipes())) {
            return '<p>No recipes</p>';
        }

        $output = "";
        foreach ($collection->getRecipes() as $recipe) {
            $output .= "<div class='recipe'>";
            $output .= '<div class="recipe-title">' . $recipe->getTitle() . '</div>';
            $output .= '<div class="recipe-author">' . $recipe->getAuthor() . '</div>';
            $output .= '<div class="recipe-servings">' . $recipe->getServings() . '</div>';
            $output .= '<div class="recipe-tag">' . $recipe->getTagName() . '</div>';
            $output .= '<h4 class="ingredients-title">Ingredients</h4>';
            $output .= '<div class="recipe-ingredients">' . $this->renderIngredients($recipe->getIngredients()) . '</div>';
            $output .= '<h4 class="instructions-title">Instructions</h4>';
            $output .= '<div class="recipe-instructions">' . $this->renderInstructions($recipe->getInstructions()) . '</div>';
            $output .= "</div>";
        }

        return $output;
    }

    public function renderIngredients(array $ingredients)
    {
        $output = '';
        foreach ($ingredients as $ing) {
            $output .= '<div class="ingredient">';
            $output .= '<span class="ingredient-amount">' . $ing->getAmount() . '</span>';
            $output .= '<span class="ingredient-measurement">' . $ing->getMeasurement() . '</span>';
            $output .= '<span class="ingredient-name">' . $ing->getName() . '</span>';
            $output .= '</div>';
        }

        return $output;
    }

    public function renderInstructions(InstructionsCollection $instructions)
    {
        $output = '';

        foreach ($instructions->getInstructions() as $instruction) {
            $output .= '<div class="instruction">';
            $output .= '<p class="instruction-text">' . $instruction->getInstruction() . '</p>';
            $output .= '</div>';
        }

        return $output;
    }
}
