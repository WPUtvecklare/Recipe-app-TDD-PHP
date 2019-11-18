<?php

class MainController
{
    private $mainView;
    private $recipeView;

    public function __construct($mainView, $recipeView)
    {
        $this->mainView = $mainView;
        $this->recipeView = $recipeView;
    }

    public function run()
    {
        if ($this->recipeView->userWantsToAddRecipe()) {
            $this->recipeView->renderAddRecipe();
        }

        return $this->mainView->render($this->recipeView);
    }
}
