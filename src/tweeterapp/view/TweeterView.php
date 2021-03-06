<?php

namespace tweeterapp\view;

use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use mf\router\Router;
use tweeterapp\auth\TweeterAuthentification;
use tweeterapp\control\TweeterAdminController;
use tweeterapp\control\TweeterController;
use tweeterapp\model\User;

class TweeterView extends \mf\view\AbstractView {
    private $router;
  
    /* Constructeur 
    *
    * Appelle le constructeur de la classe parent
    */
    public function __construct( $data ){
        parent::__construct($data);
        $this->router = new Router();
    }

    /* Méthode renderHeader
     *
     *  Retourne le fragment HTML de l'entête (unique pour toutes les vues)
     */ 
    private function renderHeader(){
        return '<div class = "theme-backcolor1"><h1>MiniTweeTR</h1></div>';
    }

    private function renderFooter(){
        return 'La super app créée en Licence Pro &copy;2019';
    }
    
    private function renderHome(){
        $homeHTML="";
        foreach($this->data as $tweet){
            $text = $tweet['text'];
            $author = $tweet['authorNickName'];
            $tweetLink = $this->router->urlFor("/tweet", ['id'=> $tweet['id']]) ;
            $authorLink = $this->router->urlFor("/author", ['id' => $tweet['author']]) ;
            $homeHTML.= <<<EOT
            <div class = "tweet">
                <div class="tweet-text"><a href="$tweetLink">$text</a></div>
                <div class="tweet-author"> <a href="$authorLink">$author</a></div>
            </div>
            <hr>
EOT;
        }
        if(isset($_SESSION['user_login'])){
            $bottomMenu=$this->renderBottomMenu();
        }
        else{
            $bottomMenu="<p>Signin to write a Tweet !</p>";
        }

        return $homeHTML.$bottomMenu;
    }
     
    private function renderUserTweets(){
        $homeHTML="";
        foreach($this->data as $tweet) {
            $text = $tweet['text'];
            $author = $tweet['authorNickName'];
            $tweetLink = $this->router->urlFor("/tweet", ['id' => $tweet['id']]);
            $authorLink = $this->router->urlFor("/author", ['id' => $tweet['author']]);
            $homeHTML .= <<<EOT
            <div class = "tweet">
                <a href="$tweetLink"><div class="tweet-text">$text</div></a>
                <div class="tweet-author"> <a href="$authorLink">$author</a></div>
            </div>
            <hr>
EOT;
        }
        return $homeHTML;
    }

    private function renderViewTweet(){
        $textTweet = $this->data['text'] ;
        return <<<EOT
        <div class="tweet">
            <div class="tweet-text"> $textTweet </div>
        </div>
EOT;
    }

    protected function renderPostTweet(){
        $actionForm = $this->router->urlFor("/send");
        return <<<EOT
        <form action ="$actionForm" method="post">
	        <textarea cols="30" rows="2" name="text">Enter Tweet...</textarea></br> 
	        <button type="submit">Send</button>
        </form>
EOT;
    }

    protected function renderLogin(){
        $actionForm = $this->router->urlFor("/checklogin");
        return <<<EOT
        <form action="$actionForm" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="password">
            <button type="submit">Connect</button>
        </form>
EOT;

    }

    protected function renderFollowers(){
        $htmlFollower="";
        if(isset($_GET['userid'])){
            $username=User::select("username")->where("id", "=", $_GET['userid'])->first();
            $htmlFollower.="<h4> Followers for : ".$username->username."</h4>";
        }
        else
            $htmlFollower.="<h4> Followers for : ".$_SESSION["user_login"]."</h4>";
        $htmlFollower .= "<p>Number of followers : ".count($this->data)."</p>";
        foreach($this->data as $follower){
            $htmlFollower.='<div class="follower">'.$follower->username.'</div>';
        }
        return $htmlFollower;
    }

    protected function renderInfluence(){
        $htmlInfluence = "<table><tr><th>Username</th><th>Nbr followers</th></tr>";
        foreach($this->data as $user){
            $influenceurLink = $this->router->urlFor("/listesuiveurs");
            $influenceurLink.="?userid=".$user->id;
            $htmlInfluence.=<<<EOT
                <tr>
                    <td><a href="$influenceurLink">$user->username</a></td>
                    <td>$user->followers</td>
EOT;
        }
        return $htmlInfluence.="</tr></table>";
    }

    protected function renderSphere(){
        $htmlInfluence = "<table><tr><th>Username</th><th>Taille Sphere</th></tr>";
        foreach($this->data as $userID => $userSphere){
            $user = User::find($userID);
            $influenceurLink = $this->router->urlFor("/listesuiveurs");
            $influenceurLink.="?userid=".$userID;
            $htmlInfluence.=<<<EOT
                <tr>
                    <td><a href="$influenceurLink">$user->username</a></td>
                    <td>$userSphere</td>
EOT;
        }
        return $htmlInfluence.="</tr></table>";
    }

    protected function renderSignup(){
        $actionForm = $this->router->urlFor("/checksignup");
        return <<<EOT
        <form action="$actionForm" method="post">
            Fullname : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="fullname" placeholder="Fullname">
            <br>
            Username : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="username" placeholder="Username">
            <br>
            Password : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" name="password" placeholder="password">
            <br>
            Retype password : <input type="password" name="retypepassword" placeholder="retype password">
            <br>
            <button type="submit">Signup</button>
        </form>
EOT;
    }

    protected function renderBottomMenu()
    {
        $postLink = $this->router->urlFor("/post");
        return <<<EOT
        <div>
            <a href='$postLink' >
                <img src="https://enywook.github.io/tuiteur/html/feather-alt-solid.svg" width="128px" height="128px" alt="homeLogged">
                <p>Write a new Tuit</p>
            </a>
        </div>

EOT;
    }

    protected function renderTopMenu(){
        $homeLink = $this->router->urlFor("/home");
        if(TweeterAuthentification::isLogged()){
            $userID = User::select("id")->where("username", "like", "%".$_SESSION["user_login"]."%")->first();
            $followersLink = $this->router->urlFor("/followers");
            $followersLink.="?id=".$userID->id;
            $logoutLink = $this->router->urlFor("/logout");
            $homeLoggedLink = $this->router->urlFor("/homeLogged");
            $homeLoggedLink.="?id=".$userID->id;
            $isAdmin = $_SESSION["access_level"]>=TweeterAuthentification::ACCESS_LEVEL_ADMIN;
            if($isAdmin){
                $influenceLink = $this->router->urlFor("/influence");
                $sphereLink = $this->router->urlFor("/sphere");
            }
            $htmlTopMenu = <<<EOT
            <div>
                <a href="$homeLink"><img src="https://enywook.github.io/tuiteur/html/home.png" alt="home"></a>
                <a href="$followersLink"><img src="https://enywook.github.io/tuiteur/html/followees.png" alt="followees"></a>
                <a href="$logoutLink"><img src="https://enywook.github.io/tuiteur/html/logout.png" alt="logout"></a>
                <a href="$homeLoggedLink"><img src="https://enywook.github.io/tuiteur/html/themeisle-brands.svg" width="128px" height="128px" alt="homeLogged"></a>
            
EOT;
            if($isAdmin){
                $htmlTopMenu.="<a href='$influenceLink'><img src='https://enywook.github.io/tuiteur/html/ruler-solid.svg' width='128px' height='128px' alt='influence'></a>"."</div>";
                return $htmlTopMenu.="<a href='$sphereLink'><img src='https://enywook.github.io/tuiteur/html/sitemap-solid.svg' width='128px' height='128px' alt='influence'></a>"."</div>";

            }
            return $htmlTopMenu.="</div>";
        }else{
            $loginLink = $this->router->urlFor("/login");
            $signupLink = $this->router->urlFor("/signup");
            return <<<EOT
                <div>
                    <a href="$homeLink"><img src="https://enywook.github.io/tuiteur/html/home.png" alt="home"></a>
                    <a href="$loginLink"><img src="https://enywook.github.io/tuiteur/html/login.png" alt="login"></a>
                    <a href="$signupLink"><img src="https://enywook.github.io/tuiteur/html/signup.png" alt="signup"></a>
                </div>
EOT;
        }
    }

    protected function renderBody($selector){
        switch ($selector) {
            case "homeLogged":
            case "home":
                $sectionContent = $this->renderHome();
                break;
            case "userTweet":
                $sectionContent = $this->renderUserTweets();
                break;
            case "viewTweet":
                $sectionContent = $this->renderViewTweet();
                break;
            case "postTweet":
                $sectionContent = $this->renderPostTweet();
                break;
            case "login":
                $sectionContent= $this->renderLogin();
                break;
            case "signup":
                $sectionContent = $this->renderSignup();
                break;
            case "followers":
                $sectionContent = $this->renderFollowers();
                break;
            case "influence":
                $sectionContent = $this->renderInfluence();
                break;
            case "sphere":
                $sectionContent = $this->renderSphere();
                break;
            default:
                $sectionContent = $this->renderHome();
        }


        $html="<body><header>".$this->renderHeader()."</header>"
            .$this->renderTopMenu()
            ."<section>".$sectionContent."</section>"
            ."<footer>".$this->renderFooter()."</footer></body>" ;
        return $html;
    }
}
