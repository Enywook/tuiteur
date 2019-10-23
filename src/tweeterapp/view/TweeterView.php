<?php

namespace tweeterapp\view;

use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use mf\router\Router;
use tweeterapp\control\TweeterController;

class TweeterView extends \mf\view\AbstractView {
  
    /* Constructeur 
    *
    * Appelle le constructeur de la classe parent
    */
    public function __construct( $data ){
        parent::__construct($data);
    }

    /* Méthode renderHeader
     *
     *  Retourne le fragment HTML de l'entête (unique pour toutes les vues)
     */ 
    private function renderHeader(){
        return '<div class = "theme-backcolor1"><h1>MiniTweeTR</h1></div>';
    }
    
    /* Méthode renderFooter
     *
     * Retourne le fragment HTML du bas de la page (unique pour toutes les vues)
     */
    private function renderFooter(){
        return 'La super app créée en Licence Pro &copy;2019';
    }

    /* Méthode renderHome
     *
     * Vue de la fonctionalité afficher tous les Tweets. 
     *  
     */
    
    private function renderHome(){
        $router = new \mf\router\Router();
        $homeHTML="";
        foreach($this->data as $tweet){
            $text = $tweet['text'];
            $author = $tweet['authorNickName'];
            $tweetLink = $router->urlFor("/tweet", ['id'=> $tweet['id']]) ;
            $authorLink = $router->urlFor("/author", ['id' => $tweet['author']]) ;
            $homeHTML.= <<<EOT
            <div class = "tweet">
            <div class="tweet-text"><a href="$tweetLink">$text</a></div>
            <div class="tweet-author"> <a href="$authorLink">$author</a></div>
            </div>
            <hr>
EOT;
        }
        return $homeHTML;
        /*
         * Retourne le fragment HTML qui affiche tous les Tweets. 
         *  
         * L'attribut $this->data contient un tableau d'objets tweet.
         * 
         */
        
        
    }
  
    /* Méthode renderUeserTweets
     *
     * Vue de la fonctionalité afficher tout les Tweets d'un utilisateur donné. 
     * 
     */
     
    private function renderUserTweets(){
        $homeHTML="";
        foreach($this->data as $tweet){
            $text = $tweet['text'];
            $homeHTML.= <<<EOT
            <p> $text </p>
            <hr>
EOT;
        }
        return $homeHTML;

        /* 
         * Retourne le fragment HTML pour afficher
         * tous les Tweets d'un utilisateur donné. 
         *  
         * L'attribut $this->data contient un objet User.
         *
         */
        
    }
  
    /* Méthode renderViewTweet 
     * 
     * Rréalise la vue de la fonctionnalité affichage d'un tweet
     *
     */
    
    private function renderViewTweet(){
        $textTweet = $this->data['text'] ;
        return <<<EOT
        <div class="tweet">
        <div class="tweet-text"> $textTweet </div>
        </div>
EOT;

        /* 
         * Retourne le fragment HTML qui réalise l'affichage d'un tweet 
         * en particulié 
         * 
         * L'attribut $this->data contient un objet Tweet
         *
         */
        
    }



    /* Méthode renderPostTweet
     *
     * Realise la vue de régider un Tweet
     *
     */
    protected function renderPostTweet(){

        return <<<EOT
        <form>
EOT;

        
        /* Méthode renderPostTweet
         *
         * Retourne la framgment HTML qui dessine un formulaire pour la rédaction 
         * d'un tweet, l'action (bouton de validation) du formulaire est la route "/send/"
         *
         */
        
    }


    /* Méthode renderBody
     *
     * Retourne la framgment HTML de la balise <body> elle est appelée
     * par la méthode héritée render.
     *
     */
    
    protected function renderBody($selector){
        $html = <<<EOT
<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <title> Tuiteur </title>
      <link rel="stylesheet" href="html/style.css">
    </head>
EOT;

        $section = "";
        switch ($selector) {
            case "home":
                $sectionContent = $this->renderHome();
                break;
            case "userTweet":
                $sectionContent = $this->renderUserTweets();
                break;
            case "singleTweet":
                $sectionContent = $this->renderViewTweet();
                break;
            default:
                $sectionContent = $this->renderHome();
        }
        echo $html."<body><header>".$this->renderHeader()."</header>"
        ."<section>".$sectionContent."</section>"
        ."<footer>".$this->renderFooter()."</footer></body>" ;
        }

        /*
         * voire la classe AbstractView
         * 
         */












    
}